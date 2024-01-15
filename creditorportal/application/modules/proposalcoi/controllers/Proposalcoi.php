<?php
header('Access-Control-Allow-Origin: *');
if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
require_once'/var/www/html/benefitz.in/fyntune-creditor-portal/vendor/autoload.php';

use Dompdf\Dompdf;
class Proposalcoi extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function downloadCOI()
    {
        $lead_id = "";
        if (isset($_GET['text']) && !empty($_GET['text'])) {
            $varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
            parse_str($varr, $url_prams);
            $lead_id = $url_prams['id'];
        }
        //echo $lead_id;exit;
        $data = array();
        $data['lead_id'] = $lead_id;

        $result = json_decode(curlFunction(SERVICE_URL . '/api/getProposalCOI', $data), true);
        $result['coi_data'] = $result['Data'];
        //echo "<pre>";print_r($result);exit;

        $this->load->view('template/customer-header.php');
        $this->load->view('proposalcoi/coi-download.php', $result);
        $this->load->view('template/customer-footer.php');

    }

    public function payment_redirection($lead_id_encrypt)
    {

        $lead_id_encrypt = htmlspecialchars(strip_tags(trim($lead_id_encrypt)));

        if ($lead_id_encrypt) {

            $result = json_decode(curlFunction(SERVICE_URL . '/api2/payment_redirection', ['lead_id_encrypt' => $lead_id_encrypt]), true);
            //echo "<pre>";print_r($result);exit;

            if ($result['success'] == 1) {

                $policy_data = $result['data'];
                $this->load->view('template/customer-header.php');
                $this->load->view('customer/thank-you.php', $policy_data);
                $this->load->view('template/customer-footer.php');
            } else if ($result['success'] == 2) {

                redirect($result['data']);
            } else if ($result['faliure'] == 1) {

                $lead_arr = $result['data'];

                $this->load->view('template/customer-header.php');
                $this->load->view('customer/error-page.php', $lead_arr);
                $this->load->view('template/customer-footer.php');
            } else if ($result['faliure'] == 2) {

                echo $result['error'];
            }
        }
    }

    public function showthankyou()
    {

        $result = json_decode(curlFunction(SERVICE_URL . '/api2/getpolicydata', ['lead_id' => 'S3E0c1V4VlNjLzhiQTh1enpmMlFXZz09']), true);

        $this->load->view('template/customer-header.php');
        $this->load->view('customer/thank-you.php');
        $this->load->view('template/customer-footer.php');
    }

    function customerotpform($record_id)
    {
        if (isset($_SESSION[$record_id])) {

            $data['lead_id'] = $record_id;
            $this->load->helper('form');
            $this->load->view('template/customer-header.php');
            $this->load->view('customer/otpconfirmation', $data);
            $this->load->view('template/footer.php');

        } else {

            $this->load->view('template/customer-header.php');
            $this->load->view('customer/tryagain.php', ['try_again' => 0]);
            $this->load->view('template/customer-footer.php');
        }
    }

    function customdetailsform()
    {
        $data = [];
        $data['firstname'] = htmlentities(strip_tags(trim($_POST['firstname'])));
        $data['lastname'] = htmlentities(strip_tags(trim($_POST['lastname'])));
        $data['address_line1'] = htmlentities(strip_tags(trim($_POST['address_line1'])));
        $data['address_line2'] = htmlentities(strip_tags(trim($_POST['address_line2'])));
        $data['address_line3'] = htmlentities(strip_tags(trim($_POST['address_line3'])));
        $data['pincode'] = htmlentities(strip_tags(trim($_POST['pin_code'])));
        $data['city'] = htmlentities(strip_tags(trim($_POST['city'])));
        $data['state'] = htmlentities(strip_tags(trim($_POST['state'])));
        $data['lead_id'] = htmlentities(strip_tags(trim($_POST['elem_lead'])));
        $data['customer_id'] = htmlentities(strip_tags(trim($_POST['elem_customer'])));

        /*$data['lead_id'] = base64_decode(strtr($data['lead_id'], '-_', '+/'));
        $data['customer_id'] = base64_decode(strtr($data['customer_id'], '-_', '+/'));*/

        $data['lead_id'] = encrypt_decrypt_password($data['lead_id'], 'D');
        $data['customer_id'] = encrypt_decrypt_password($data['customer_id'], 'D');

        $result = curlFunction(SERVICE_URL . '/api2/savecustomerdetails', $data);

        //$_SESSION['lead_id'] = $_POST['elem_lead'];
        echo $result;
        exit;
    }

    function memberdetailsform()
    {
        $data = [];
        $data['firstname'] = htmlentities(strip_tags(trim($_POST['firstname'])));
        $data['lastname'] = htmlentities(strip_tags(trim($_POST['lastname'])));

        /*$data['lead_id'] = base64_decode(strtr($_POST['elem_lead'], '-_', '+/'));
        $data['customer_id'] = base64_decode(strtr($_POST['elem_customer'], '-_', '+/'));
        $data['member_id'] = base64_decode(strtr($_POST['elem_member'], '-_', '+/'));*/

        $data['lead_id'] = encrypt_decrypt_password($_POST['elem_lead'], 'D');
        $data['customer_id'] = encrypt_decrypt_password($_POST['elem_customer'], 'D');
        $data['member_id'] = encrypt_decrypt_password($_POST['elem_member'], 'D');

        $result = curlFunction(SERVICE_URL . '/api2/savememberdetails', $data);
        //$_SESSION['lead_id'] = $_POST['elem_lead'];
        echo $result;
        exit;
    }

    function verifyotp()
    {

        $data['lead_id'] = $lead_id = htmlentities(strip_tags(trim($_POST['lead_id'])));
        $data['otp'] = htmlentities(strip_tags(trim($_POST['otp'])));

        $addEdit = json_decode(curlFunction(SERVICE_URL . '/api2/checkLeadotpCustomer', $data), true);

        if ($addEdit['status_code'] == '200') {

            unset($_SESSION[$lead_id]);
            //$_SESSION['lead_id'] = $data['lead_id'];
            echo '1';
        } else {

            echo '0';
        }

        exit;
    }

    function customerpaymentformdetails($lead_id)
    {

        $response = json_decode(curlFunction(SERVICE_URL . '/api2/checkIfOtpRequired', ['lead_id' => $lead_id]), true);

        if ($response['response'] == 1) {

            $data['lead_id'] = encrypt_decrypt_password($lead_id, 'D');
            $data['customer'] = true;

            $result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);

            $customers = reset($result['customer_details']);

            foreach ($customers as $customer) {

                $requestData = [
                    'customer_id' => $customer['customer_id'],
                    'lead_id' => encrypt_decrypt_password($lead_id, 'D')
                ];

                $mode = 'addEdit';
                $members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
                $questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
                $answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

                $customer_id = $customer['customer_id'];

                $html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
                $result['ghd_declaration'][$customer['customer_id']] = $html;
            }

            $result['lead_id_enc'] = $lead_id;
            $_SESSION[$lead_id] = $lead_id;

            //unset($_SESSION['lead_id']);

            $this->load->helper('form');
            $this->load->view('template/customer-header.php');
            $this->load->view('customer/addEditCustomer', $result);
            $this->load->view('template/customer-footer.php');
        } else if ($response['response'] == 2 || $response['response'] == 3) {

            redirect(base_url() . 'paymentgatewayredirect/' . $lead_id);
        } else {

            $this->load->view('template/customer-header.php');
            $this->load->view('customer/tryagain.php', ['try_again' => 0]);
            $this->load->view('template/customer-footer.php');
        }

        /*if(isset($_SESSION['lead_id']) && $_SESSION['lead_id'] != ''){

            $result = array();
            $data = array();

            /*if (isset($_SESSION['webpanel']['customerotp']) && isset($_SESSION['webpanel']['customerlead'])) {
                $data['lead_id'] = $_SESSION['webpanel']['customerlead'];
                $data['otp'] = $_SESSION['webpanel']['customerotp'];
                $result['leaddetails'] = json_decode(curlFunction(SERVICE_URL . '/api2/getLeadDetailsCustomer', $data));
                $this->load->view('template/header.php');
                $this->load->view('policyproposal/addEditCustomer', $result); //Payment form link needs to be added here
                $this->load->view('template/footer.php');
            } else {
                $data['lead_id'] = $_POST['lead_id'];
                $data['otp'] = $_POST['otp'];
                $addEdit = json_decode(curlFunction(SERVICE_URL . '/api2/checkLeadotpCustomer', $data));
                if ($addEdit['status_code'] == '200') {
                    $_SESSION['webpanel']['customerlead'] = $addEdit['Data']['lead_id'];
                    $_SESSION['webpanel']['customerotp'] = $addEdit['Data']['otp'];
                    echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message']));
                    exit;
                } else {
                    echo json_encode(array('success' => false, 'msg' => $addEdit['Metadata']['Message']));
                    exit;
                }
            }*/

        /*$lead_id = $_SESSION['lead_id'];

        $data['lead_id'] = encrypt_decrypt_password($lead_id, 'D');
        $data['customer'] = true;

        $result = json_decode(curlFunction(SERVICE_URL . '/api2/getProposalSummary', $data), true);

        $customers = reset($result['customer_details']);

        foreach ($customers as $customer) {

            $requestData = [
                'customer_id' => $customer['customer_id'],
                'lead_id' => encrypt_decrypt_password($lead_id, 'D')
            ];

            $mode = 'addEdit';
            $members = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyAddedMembers', $requestData));
            $questions = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDQuestions', []));
            $answers = json_decode(curlFunction(SERVICE_URL . '/api2/getGHDAnswers', $requestData));

            $customer_id = $customer['customer_id'];

            $html = $this->load->view('policyproposal/ghddeclaration', compact('mode', 'members', 'questions', 'answers', 'customer_id'), true);
            $result['ghd_declaration'][$customer['customer_id']] = $html;
        }

        $result['lead_id_enc'] = $lead_id;

        //unset($_SESSION['lead_id']);

        $this->load->helper('form');
        $this->load->view('template/customer-header.php');
        $this->load->view('customer/addEditCustomer', $result);
        $this->load->view('template/customer-footer.php');
    }
    else {

        $prev_url = $_SESSION['prev_url'];
        redirect($prev_url);
    }*/
    }

    public function coi_download()
    {

        $certificate_no = $this->input->post('certificate_no');
        $certificate_no = htmlspecialchars(strip_tags(trim($certificate_no)));

        $data = ['certificate_no' => $certificate_no];
        echo curlFunction(SERVICE_URL . '/api2/coi_download', $data);
    }

    public function coidownload()
    {


        $leadId = $_GET['lead_id'];
        $req_data['lead_id'] = $leadId;
        {
            if (empty($leadId))

                $req_data['lead_id'] = $_POST['lead_id'];
        }
        //print_r($req_data['lead_id']);exit;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data), TRUE);
        $premiumDetails = $data['premium_details'];
        //print_r($data['premium_details']);exit;
        if (isset($data['data']) && !empty($data['data'])) {

            $data = $data['data'];
            $data['premium_details'] = $premiumDetails;
            $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                'lead_id' => encrypt_decrypt_password($_POST['lead_id']),


            ]), TRUE);
            //print_r($checkDetails);exit;

            $data['insured_member'] = $checkDetails;

        }
        $html = $this
            ->load
            ->view("quotes/coi_pdf", $data, true);
        echo $html;

    }

    public function coidownloadAPI11()
    {


        $leadId = $_GET['lead_id'];
        $req_data['lead_id'] = $leadId;

        //print_r($req_data['lead_id']);exit;
        $data = json_decode(curlFunction(SERVICE_URL . '/customer_api/getCOiinfo', $req_data), TRUE);
        $premiumDetails = $data['premium_details'];
        if (isset($data['data']) && !empty($data['data'])) {

            $data = $data['data'];
            $data['premium_details'] = $premiumDetails;
            $checkDetails = json_decode(curlFunction(SERVICE_URL . '/customer_api/getMember_insure_details_coi', [

                'lead_id' => encrypt_decrypt_password($_GET['lead_id']),


            ]), TRUE);
            // echo $this->db->last_query();
            //  print_r($checkDetails);exit;

            $data['insured_member'] = $checkDetails;

        }

        $html = $this->get_html($data);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("", array("Attachment" => false));
        //echo $html;

    }

    function get_html($data)
    {
        extract($data);

        $html = '
        <html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Open Sans\', \'Helvetica Neue\', sans-serif;
            font-size: 14px;
        }

        

        table,
        th,
        td {
            border: 1px solid #8e8e8e;
            border-collapse: collapse;
            padding: 5px;
            width: 22%;
            text-align: center;
            font-size: 13px !important;
        }

        .content_pdf {
            padding-left: 15px;
            padding-right: 15px;
        }

        .header_title {
            font-weight: 600;
            font-size: 18px;
            color: #000;
            margin: 0px;
            padding: 6px;
            font-size: 15px;
            text-align: left;
        }

        .table_content_space {
            padding-top: 15px;
            padding-bottom: 15px;
        }

        table {
            width: 100%;
        }

        .text_left td {
            text-align: left;
        }

        .pdf_flex {
            display: flex;
        }

        .pdf_flex p {
            padding-right: 5%;
        }

        .pdf_flex3 p {
            font-size: 12px;
        }

        .pdf_flex3 {
            display: flex;
        }

        .card_health {
            border: 2px solid #696969;
            padding: 10px;
            margin-top: 15px;
            border-radius: 26px;
        }

        .card_1111 {
            width: 32%;
        }

         .new-page {
    page-break-before: always;
  }

        @media print {
  .new-page {
    page-break-before: always;
  }
}
    </style>
</head>

<body>
    <div class="container_pdf">
        
        <div class="content_pdf">
            <div class="pdf_header">
                <p class="header_title" style="background: #fff;
                        color: #cb2434;
                    font-size: 18px; text-align: left;">' . $creaditor_name . ' - ' . $plan_name . ' - Certificate of Insurance</p>
            </div>
            <div>
                <div class="table_content_space">
                    <table>
                        <tr>
                            <td>Policy Issuing Office</td>
                            <td style="font-weight: bold;">10th Floor,
                                R-Tech Park, Nirlon Compound, Goregaon-East, Mumbai-400063</td>
                            <td>Policy Servicing Office </td>
                            <td style="font-weight: bold;">G B Rain Tree Place No-7 MC Nichols Road
                                ChetpetCHENNAITAMIL NADU600031</td>
                        </tr>
                        <tr>
                            <td>Master Policy Number </td>
                            <td style="font-weight: bold;">' . $policy_number . '</td>
                            <td>Certificate Number</td>
                            <td style="font-weight: bold;">' . $certificate_number . ' 
                            </td>
                        </tr>
                        <tr>
                            <td>Master Policy Holder Name</td>
                            <td style="font-weight: bold;">' . $cust_details . '</td>

                        </tr>
                        <tr>
                            <td>Product Name</td>
                            <td style="font-weight: bold;">' . $creaditor_name . ' - ' . $plan_name . '</td>

                        </tr>
                        <tr>
                            <td>Plan Name</td>
                            <td style="font-weight: bold;">' . $plan_name . ' - ' . $policy_sub_type_name . ' </td>
                            <td>Member Id</td>
                            <td style="font-weight: bold;">HIN100011795</td>
                        </tr>
                        <tr>
                            <td>Name of Insured Person Residential Address of Insured Person</td>
                            <td style="font-weight: bold;"> Hemant Shah G B Rain Tree Place No-7 MC
                                Nichols Road ChetpetCHENNAITAMIL
                                NADU600031</td>
                            <td>Unique Identification
                                Number</td>
                            <td style="font-weight: bold;">KL0156651651
                            </td>
                        </tr>
                        <tr>
                            <td>Contact Details</td>
                            <td style="font-weight: bold;">9619902090
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="table_content_space">
                <table class="text_left">
                    <tr>
                        <td>
                            Start date & Time of Master Policy
                        </td>
                        <td style="font-weight: bold;">
                            00:01 hrs <?php echo date(\'d/m/Y\',strtotime($start_date)); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Expiry Date & Time of Master Policy</td>
                        <td style="font-weight: bold;">23:59 on <?php echo date(\'d/m/Y\',strtotime($end_date)); ?> </td>
                    </tr>
                    <tr>
                        <td>Period of Insurance</td>
                        <td style="font-weight: bold;"></td>
                    </tr>
                    <tr>
                        <td>Inception Date</td>
                        <td style="font-weight: bold;">00:01 hrs <?php echo date(\'d/m/Y\',strtotime($start_date)); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>End Date</td>
                        <td style="font-weight: bold;">23:59 on <?php echo date(\'d/m/Y\',strtotime($end_date)); ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="table_content_space">
                <div class="pdf_header">
                    <p class="header_title">Insured Person Detail
                    </p>
                </div>';

        if(count($insured_member) > 0){
            foreach ($insured_member as $key => $value_m) {
                $html .='  <p class="header_title">'.$value_m['policy_sub_type_name'].'
                </p>
                <table>
                    <tr>
                        <th>Insured Person</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Nominee</th>
                        <th>Relationship</th>
                        <th>Sum Insured</th>
                       
                    </tr>
                 ';
                foreach ($value_m['member'] as $value) {

                    $html .= "<tr><td>" . $value['policy_member_first_name'] . " " . $value['policy_member_last_name'] . "</td>";
                    $html .= "<td>" . $value['policy_member_dob'] . "</td>";
                    $html .= "<td>" . $value['policy_member_gender'] . "</td>";
                    $html .= "<td>" . $nominee_first_name . " " . $nominee_last_name . "</td>";
                    $html .= "<td>" . $value['member_type'] . "</td>";
                    $html .= "<td>" . $value['cover'] . "</td>";
                    $html .= "</tr>";

                }
            }
        }

        $html .= '
</table>
            </div>';


        $html .= ' <div class="table_content_space new-page">
                <div class="">
                    <p class="header_title">Premium Details
                    </p>
                </div>
                <table>
                    <tr>
                        <th>Particulars</th>
                        <th>Amount</th>
                    </tr>
                    <tr>
                        <td>Net Premium</td>
                        <td>' . $premium_details['premium'] .'</td>
                    </tr>
                   
                    <tr>
                        <td>Tax Percent</td>
                        <td>' .  round((($premium_details['premium_with_tax']-$premium_details['premium'])/$premium_details['premium']) *100)."%".'</td>
                    </tr>
                  
                    <tr>
                        <td>Gross Premium</td>
                        <td>'.  $premium_details['premium_with_tax'].'</td>
                    </tr>
                    <tr>
                        <td>Premium payment mode</td>
                        <td>Monthly </td>
                    </tr>
                </table>
            </div>

            <div class="pdf_flex" style="text-align: center;">
                <p class=" card_1111"> GST Registration No :<span style="font-weight: bold;">27AANCA4062G1ZN</span></p>
                <p class=" card_1111">Category: <span style="font-weight: bold;">General Insurance</span></p>
                <p class=" card_1111">SAC Code: <span style="font-weight: bold;"> 997133</span></p>
            </div>

            <div class="table_content_space">
                <div class="pdf_header">
                    <p class="header_title">Claim Process
                    </p>
                </div>
                <table border=2 cellpadding=4>
                    <tr>
                        <th rowspan=3>Please contact us through
                            any of these Modes
                        </th>
                        <td>Address for Correspondence</td>
                        <td>Aditya Birla Health Insurance Co. Limited
                            Claims Dept. 5th floor, C building, Modi Business Centre, Kasarvadavali,
                            Mumbai, Thane West 400 615
                        </td>
                    </tr>
                    <tr>
                        <td>Contact Number</td>
                        <td>1800 270 7000</td>
                    </tr>
                    <tr>
                        <td>Email ID</td>
                        <td>care.healthinsurance@adityabirlacapital.com</td>
                    </tr>
                </table>
            </div>
            <div class="table_content_space">
                <div class="pdf_header">
                    <p class="header_title gd-txt">Greviance Redressal
                    </p>
                </div>
                <p style="font-size: 10px;">In case of a grievance, the Insured Person/ Policyholder can contact Us with
                    the details through our website:
                    www.adityabirlacapital.com,Email:care.healthinsurance@adityabirlacapital.com or Toll Free : 1800 270
                    7000. Address: Any of Our Branch office or
                    Corporate office. For senior citizens, please contact respective branch office of the Company or
                    call at 1800 270 7000 or write an e- mail at
                    seniorcitizen.healthinsurance@adityabirlacapital.com. The Insured Person can also walk-in and
                    approach the grievance cell at any of Our branches. If
                    in case the Insured Person is not satisfied with the response, then they can contact Our Head of
                    Customer Service at the following email
                    carehead.healthinsurance@adityabirlacapital.com. If the Insured Person is still not satisfied with
                    Our redressal, he/she may approach the nearest
                    Insurance Ombudsman. The contact details of the Ombudsman offices are provided on Our website and in
                    the Policy
            </div>

            <div class="table_content_space">
                <div class="">
                    <p class="header_title gd-txt txt-premium">Premium Certificate
                    </p>
                </div>
                <p >Premium Certificate is for the purpose of deduction under Section 80-(D)
                    of Income Tax (Amendment) Act 1986.
                    <br>
                    This is to certify that <span></span> paid INR<span></span>towards Premium for Health Insurance for
                    the Period from
                    00:01 on <span></span> to midnight <span></span>.
                </p>
                <table>
                    <tr>
                        <td>Instrument Number</td>
                        <td>Instrument Date</td>
                        <td>Amount</td>
                        <td>Name of the Bank</td>
                    </tr>
                    <tr>
                        <td>Inst74567890 </td>
                        <td>22-10-2019</td>
                        <td>'. $premium_details['premium_with_tax'].'</td>
                        <td>Citibank</td>
                    </tr>
                </table>
            </div>
            <p class="new-page"><b>Stamp Duty</b> -: The stamp duty of INR 1/- paid vide MH011444489201920M dated 01/02/2020, received
                from Stamp Duty Authorities vide Receipt No./
                GRASS DEFACE NO 0006038796201920 dated 05/02/2020, payment has been made vide Letter of Authorisation
                No.
                CSD/315/2020/862/2020 dated 27/02/2020 from Main Stamp Duty Office.
            </p>
            <table>
                <tr>
                    <td class="wh-tbl">Master Policy Number: <b><?php echo $policy_number;?></b></td>
                    <td class="wh-tbl">Certificate Number: <b><?php echo $certificate_number;?></b></td>
                </tr>
                <tr>
                    <td class="wh-tbl">Date:<b>23-10-2019</b></td>
                    <td class="wh-tbl">Place: <b>Mumbai</b></td>
                </tr>
            </table>
            <p ><b>Note :</b> Amount is inclusive of all taxes and cesses as applicable. This certificate must be
                surrendered to the Insurance Company for issuance of fresh
                certificate in case of cancellation of Master Policy or any alteration in the insurance affecting the
                premium</p>

            <div>
                <div class="table_content_space">
                    <p style="font-weight: bold; text-align: left; border: 1px solid #fff;">Section II : Base Covers</p>
                    <table class="tbl">
                        <tbody>
                            <tr>
                                <td class="wd-10"></td>
                                <td style="font-weight: bold; text-align: left;">Base Covers</td>
                                <td style="font-weight: bold; text-align: left;">Coverage</td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.1</td>
                                <td>In-patient Hospitalization</td>
                                <td>INR 5000000<br>
                                </td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.2</td>
                                <td>Day Care Treatment</td>
                                <td>527 listed procedures</td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.3</td>
                                <td>Domiciliary Hospitalization </td>
                                <td>Covered upto full Sum Insured</td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.4</td>
                                <td>Pre - hospitalization Medical Expenses</td>
                                <td>60 days</td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.5</td>
                                <td>Post - hospitalization Medical Expenses </td>
                                <td>90 days</td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.6</td>
                                <td>Organ Donor Expenses</td>
                                <td>Covered up to full Sum Insured</td>
                            </tr>
                            <tr>
                                <td class="wd-10">1.7</td>
                                <td>Road Ambulance Expenses </td>
                                <td>Covered up to Rs. 2500 in case of emergency</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="table_content_space">
                <p style="font-weight: bold; text-align: left; border: 1px solid #fff;">Section IV : Waivers and
                    Discounts</p>
                <table class="tbl">
                    <tbody>
                        <tr>
                            <td class="wd-10">41</td>
                            <td>Pre - Existing Disease Waiting Period</td>
                            <td>Covered after waiting period of 2 years
                                Pre-Existing illness waiting period for ABCD conditions: ABCD conditions will be
                                covered after initial waiting period of 30 days and Juvenile conditions will not be
                                payable in the policy.
                                (A= Asthma, B= Blood Pressure, C= Cholesterol & D=Diabetes)
                            </td>
                        </tr>
                        <tr>
                            <td class="wd-10">42</td>
                            <td>Two Year Waiting Period</td>
                            <td>2 Yrs applicable as define in ABHI Group Health policy
                            </td>
                        </tr>
                        <tr>
                            <td class="wd-10">43</td>
                            <td> 30 Days Waiting Period </td>
                            <td>Applicable </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table_content_space">
                <p>Pre Existing Disease</p>
                <table class="text_left">
                    <tr>
                        <td>
                            Member Name
                        </td>
                        <td>
                            Relationship
                        </td>
                        <td>Pre Existing Disease</td>
                    </tr>
                    <tr>
                        <td>.</td>
                        <td>.</td>
                        <td>.</td>
                    </tr>
                </table>
            </div>
            <div class="table_content_space new-page">
                <p class="header_title">Annexure I - Permanent Exclusion</p>
                <table class="text_left" style="text-align: left !important; word-break: break-all;">
                    <tr>
                        <td style="font-size: 9px !important;">
                            WeshallnotbeliabletomakeanypaymentforanyclaimunderanyBenefitinrespectofanyInsuredPersondirectlyorindirectlycausedby,basedon,arisingoutof,
                            relatingtoorhowsoeverattributabletoanyofthefollowing:<br>
                            1.Treatment directly or indirectly arising from or consequent upon war or any act of war,
                            invasion, act of foreign enemy, war like operations
                            (whether war be declared or not or caused during service in the armed forces of any
                            country), civil war, public defense, rebellion, uprising,
                            revolution, insurrection, military or usurped acts, nuclear weapons / materials, chemical
                            and biological weapons, ionizing radiation, contamination
                            by radioactive material or radiation of any kind, nuclear fuel, nuclear waste<br>
                            2.Committing or attempting to commit a breach of law with criminal intent, intentional self-
                            Injury or attempted suicide while Insured Person is sane
                            or insane<br> 3.Willful or deliberate exposure to danger, intentional self- Injury, non-
                            adherence to
                            Medical Advice, participation or involvement in naval, military
                            or air force operation, circus personnel, racing in wheels or horseback, diving, aviation,
                            scuba diving, parachuting, hang-gliding, rock or mountain
                            climbing, bungee jumping, parasailing, ballooning, skydiving, river rafting, polo, snow and
                            ice sports in a professional or semi- professional nature<br> 4.Abuse or the consequences of
                            the abuse of intoxicants or hallucinogenic substances such as
                            intoxicating drugs and alcohol, including smoking
                            cessation programs and the treatment of nicotine addiction or any other substance abuse
                            treatment or services, or supplies<br> 5.Weight management programs or treatment in relation
                            to the same including vitamins and
                            tonics, treatment of obesity (including morbid obesity)<br> 6.Treatment for correction of
                            eye sight due to refractive error including routine
                            examination<br> 7.All routine examinations and preventive health check-ups<br> 8.Cosmetic,
                            aesthetic and re-shaping treatments and Surgeries:<br>
                            9.Plastic Surgery or cosmetic Surgery or treatments to change appearance unless medically
                            required and certified by the attending Medical
                            Practitioner for reconstruction following an Accident, cancer or burns<br> 10.Circumcisions
                            (unless necessitated by Illness or Injury and forming part of treatment);
                            aesthetic or change-of-life treatments of any description
                            such as sex transformation operations<br> 11.Non- allopathic treatment, except as per
                            coverage of AYUSH Treatment<br> 12.Conditions for which treatment could have been done on an
                            out-patient basis without any
                            Hospitalization<br> 13.Unproven/Experimental treatment, investigational treatment, devices
                            and pharmacological
                            regimens<br> 14.Admission primarily for diagnostic purposes not related to Illness for which
                            Hospitalization has been done<br> 15.Convalescence (except as per the coverage as coverage
                            defined in Section 11 - Recovery
                            Benefit), cure, rest cure, sanatorium treatment,
                            rehabilitation measures, private duty nursing, respite care, long-term nursing care or
                            custodial care.
                            16.Preventive care, vaccination including inoculation and immunizations (except in case of
                            post-bite treatment); any physical, psychiatric or
                            psychological examinations or testing<br>
                            17.Admission for enteral feedings (infusion formulas via a tube into the upper
                            gastrointestinal tract) and other nutritional and electrolyte
                            supplements unless certified to be required by the attending Medical Practitioner as a
                            direct consequence of an otherwise covered claim.<br>
                            18. Hearing aids, spectacles or contact lenses including optometric therapy, multifocal
                            lens.<br>
                            19.Treatment for alopecia, baldness, wigs, or toupees, and all treatment related to the
                            same.<br>
                            20.Medical supplies including elastic stockings, diabetic test strips, and similar
                            products.<br>
                            21.Any expenses incurred on prosthesis, corrective devices external durable medical
                            equipment of any kind, like wheelchairs crutches, instruments
                            used in treatment of sleep apnea syndrome or continuous ambulatory peritoneal dialysis
                            (C.A.P.D.) and oxygen concentrator for bronchial asthmatic
                            condition, cost of cochlear implant(s) unless necessitated by an Accident or required
                            intra-operatively. Cost of artificial limbs, crutches or any other
                            external appliance and/or device used for diagnosis or treatment (except when used
                            intra-operatively).<br>
                            22.Psychiatric or psychological disorders, mental disorders (including mental health
                            treatments), Parkinson and Alzheimer’s disease, general debility
                            or exhaustion (“rundown condition”), sleep-apnea, stress.
                            23.External Congenital Anomalies, diseases or defects, genetic disorders.<br>
                            24.Stem cell therapy or surgery, or growth hormone therapy
                        </td>
                        <td style="font-size: 9px !important;">
                            25.Venereal disease, all sexually transmitted disease or Illness including
                            but not limited to genital warts, Syphilis, Gonorrhea, Genital Herpes,
                            Chlamydia, Pubic Lice and Trichomoniasis<br> 26.“AIDS” (Acquired Immune Deficiency Syndrome)
                            and/or infection
                            with HIV (Human Immunodeficiency Virus) including but not limited to
                            conditions related to or arising out of HIV/AIDS such as ARC (AIDS
                            Related Complex), Lymphomas in brain, Kaposi’s sarcoma, tuberculosis<br> 27.Complications
                            arising out of pregnancy (including voluntary
                            termination), miscarriage (except as a result of an Accident or Illness),
                            maternity or birth (including caesarean section) except in the case of
                            ectopic pregnancy for In-patient only<br> 28.Treatment for sterility, infertility,
                            sub-fertility or other related
                            conditions and complications arising out of the same, assisted conception,
                            surrogate or vicarious pregnancy, birth control, and similar procedures
                            contraceptive supplies or services including complications arising due to
                            supplying services<br> 29.Expenses for organ donor screening, or save as and to the extent
                            provided for in the treatment of the donor (including Surgery to remove
                            organs from a donor in the case of transplant Surgery)<br> 30.Admission for Organ Transplant
                            but not compliant under the
                            Transplantation of Human Organs Act, 1994 (amended)<br> 31.Treatment and supplies for
                            analysis and adjustments of spinal
                            subluxation, diagnosis and treatment by manipulation of the skeletal
                            structure; muscle stimulation by any means except treatment of fractures
                            (excluding hairline fractures) and dislocations of the mandible and
                            extremities<br> 32.Dentures and artificial teeth, Dental Treatment and Surgery of any
                            kind, unless requiring Hospitalization due to an Accident<br> 33.Cost incurred for any
                            health check-up or for the purpose of issuance
                            of medical certificates and examinations required for employment or
                            travel or any other such purpose<br> 34.Artificial life maintenance, including life support
                            machine used to
                            sustain a person, who has been declared brain dead, as demonstrated by:
                            1. Deep coma and unresponsiveness to all forms of stimulation; or 2<br> Absent pupillary
                            light reaction; or 3. Absent oculovestibular and corneal
                            reflexes; or 4. Complete apnea<br> 35.Treatment for developmental problems, learning
                            difficulties e.g<br> Dyslexia, behavioral problems including
                            attention deficit hyperactivity disorder (ADHD)<br> 35.Treatment for Age Related Macular
                            Degeneration (ARMD),
                            treatments such as Rotational Field Quantum Magnetic Resonance
                            (RFQMR), External Counter Pulsation (ECP), Enhanced External
                            Counter Pulsation (EECP), Hyperbaric Oxygen Therapy<br> 36. Expenses which are medically not
                            required such as items of
                            personal comfort and convenience including but not limited to television
                            (if specifically charged), charges for access to telephone and telephone
                            calls (if
                            specifically charged), food stuffs (save for patient’s diet), cosmetics,
                            hygiene articles, body care products and bath additives, barber
                            expenses, beauty service, guest service as well as similar incidental
                            services and supplies, vitamins and tonics unless certified to be required
                            by the attending Medical Practitioner as a direct consequence of an
                            otherwise covered claim<br> 37.Treatment taken from a person not falling within the scope of
                            definition of Medical Practitioner<br> 38.Treatment charges or fees charged by any Medical
                            Practitioner acting
                            outside the scope of license or registration granted to him by any
                            medical council<br> 39.Treatments rendered by a Medical Practitioner who is a member of
                            the Insured Person’s family or stays with him, save for the proven
                            material costs are eligible for reimbursement as per the applicable cover<br> 40.Any
                            treatment or part of a treatment that is not of a reasonable
                            charge, is not a Medically Necessary Treatment; drugs or treatments
                            which are not supported by a prescription<br> 41.Charges related to a Hospital stay not
                            expressly mentioned as being
                            covered, including but not limited to charges for admission, discharge,
                            administration, registration, documentation and filing, including MRD
                            charges (medical records department charges)<br> 42.Non-medical expenses including but not
                            limited to RMO charges,
                            surcharges, night charges, service charges levied by the Hospital under
                            any head and as specified in the Annexure V for non- medical expenses<br> 43.Treatment taken
                            outside India<br> 44.Insured Person whilst flying or taking part in aerial activities except
                            as a fare-paying passenger in a regular scheduled airline or air charter
                            company<br> For detailed policy wordings regarding the above please visit our
                            website
                            <a
                                href="https://www.adityabirlahealth.com/healthinsurance/#!/downloads">https://www.adityabirlahealth.com/healthinsurance/#!/downloads</a>
                        </td>
                    </tr>
                </table>
                <p style="color: #444; font-size: 8px;">*This is a computer generated statement doesn’t need any
                    signature</p>
            </div>
            <div class="table_content_space">
                <div class="pdf_header">
                    <p class="header_title gd-txt txt-premium new-page" style="text-align: left !important;">Pre Existing
                        Disease:
                    </p>
                </div>
                <table>
                    <tr>
                        <td>Member Name</td>
                        <td>Relationship</td>
                        <td>Pre Existing Disease</td>
                    </tr>
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                </table>
                <b style="font-size: 12px;">Your e-health card is appended below</b>
            </div>';
        $html .='    <div class="fl-card">
                <div class="fl-1 card-lf" style="margin: 5px;">

                    <div class="pd-lf-40">
                        <img src="'.base_url().'assets/coi_images/ad_head.png" width="100%"><br>
                        <div class="pf-50">
                            <span class="sp-txt">Toll Free No: </span> <span class="yl-txt" style="font-size: 15px;
                        font-weight: bolder;"> 1800 270 7000</span>
                        </div>
                        <br><br>
                        <div style="width: 100%; padding-left: 4%;">
                            <p class="sp-txt">Website : <span class="yl-txt"
                                    style="font-size: 11px;">adityabirlacapital.com</span></p>
                            <p class="sp-txt">Email : <span class="yl-txt" style="font-size: 11px;">
                                    care.healthinsurance@adityabirlacapital.com</span> </p>
                        </div>
                    </div>
                    <!-- <img src="card.PNG" width="90%"> -->
                </div>
                <div class="fl-2 ">
                    <div class="fl-cd">
                        <div class="card-fl">
                            <div class="ad-text">Aditya Birla Health Insurance Co. Limited</div>
                            <p style="font-size: 6px;">POLICY No - '. $policy_number.'</p>
                            <p style="font-size: 6px;">Certificate No - '. $certificate_number.'</p>
                            <div class="fl-card">
                                <div class="fl-1" style="font-size: 6px;">COVERAGE START DATE - '. date('d/m/Y',strtotime($start_date)).'</div>
                                <div class="fl-2" style="font-size: 6px;">COVERAGE END DATE - '. date('d/m/Y',strtotime($end_date)).'</div>
                            </div>
                        </div>

                        <div>
                            <table class="text_left fl-td">
                                <tr>
                                    <td style="font-weight: bold;">
                                        Name
                                    </td>
                                    <td style="font-weight: bold;">
                                        Membership No.
                                    </td>
                                    <td style="font-weight: bold;">
                                        DOB
                                    </td>
                                    <td style="font-weight: bold;">
                                        Relationship
                                    </td>
                                </tr>
                                <tr>
                                    <td>Self</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Spouse</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>kid 1</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Kid 2</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Kid 3</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Kid 4</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <ul class="card-ft">
                            <li>This card is for identification and is not an authorization to proceed with the
                                treatment or
                                a guarantee for payment.</li>

                            <li>
                                In the case of photoless identify cards issued to beneficiaries, acceptable proof of
                                identity such as Aadhar Card/ Passport/ Driver License/ Ration Card/ Voters Id Card/ PAN
                                Card should be presented at hospitalization
                            </li>
                            <li>
                                This non-transferable identfication card is valid at selected Network Hospitals & will
                                enable Card Holder to avail cashless hospitalizatiob only o the basis of
                                preauthorization by
                                Aditya Birla Health Insrance Co Ltd
                            </li>
                            <li>
                                For the latest updated Network Hospital List, login
                                htppa://www.adityabirlacapital.com/healthinsurance/#!/provider-search
                            </li>
                        </ul>
                    </div>

                    <div class="fl-card fl-cd-22">
                        <div class="fl-12">
                            Aditya Birla Health Insurance Co. Limited. IRDAI Reg. 153. CIN No. U66000MH2015PLC263677.
                            Website: adityabirlahealthinsuarnce.com Fax: 02262257700 Disclaimer:
                            Trademark/Logo Aditya Birla Capital logo is owned by Aditya Birla Management Corporation
                            Private Limited and is used by Aditya Birla Health Insurance Co. Limited under licensed user
                            agreement(s). </div>

                        <div class="fl-22">
                            <img src="'. base_url().'assets/coi_images/logo-ad.png" width="100" style="margin-top: -4px;">
                        </div>
                    </div>

                </div>

            </div>
            <br>
            <br>
            

        </div>

        <div>
        </div>
    </div>
</body>

</html>
        ';
      return $html;
    }
}
