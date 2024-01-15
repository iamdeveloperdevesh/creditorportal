<?php defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use function PHPSTORM_META\type;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once PATH_VENDOR.'vendor/autoload.php';

require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/SMTP.php';
// created by Upendra - on 16-10-2021
if (!function_exists('common_captcha_create'))
{
	function common_captcha_create($captcha_string)
	{	
		// echo"coming";exit;

		$ci =& get_instance();
		$ci ->load->library('image_lib');
        $ci ->load->helper('captcha');
        $vals = array(
            'word'          => $captcha_string,
            'img_path'      => 'assets/captchaimages/',
            'img_url'       =>  base_url().'assets/captchaimages/',
            'font_path'     => './path/to/fonts/texb.ttf',
            // 'font_path'     => './public/assets/images/texb.ttf',
            'img_width'     => '150',
            'img_height'    => '30',
            'expiration'    => '7200',
            'word_length'   => '6',
            'font_size'     => '16',
            'img_id'        => 'captcha_image_load',
            'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    
            // White background and border, black text and red grid
            'colors'        => array(
                    'background' => array(255, 255, 255),
                    'border' => array(255, 255, 255),
                    'text' => array(0, 0, 0),
                    'grid' => array(255, 40, 40)
            )
    );
    
    $cap = create_captcha($vals);
	// echo"Coming";
	// print_r($vals['img_path']);exit;
	return $cap;
    
     }
}

if (!function_exists('CheckCDThreshold')) {
    function CheckCDThreshold($creditor_id,$plan_id,$collection_amount=0)
    {
        $CI = &get_instance();
        $plan_payment_mode=$CI->db->query("select payment_mode_id  from plan_payment_mode  where master_plan_id = '$plan_id' and payment_mode_id=4");
        //echo $CI->db->last_query();die;
        if($CI->db->affected_rows() > 0){
            $amount_utilzed = $CI->db->query("select sum(mt.amount) as amount_utilzed from master_cd_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type=2 and mt.creditor_id=" . $creditor_id)->row()->amount_utilzed;

            $amount_deposite = $CI->db->query("select sum(mt.amount) as amount_deposite from master_cd_credit_debit_transaction mt
where mt.type=1 and mt.creditor_id=" . $creditor_id)->row()->amount_deposite;
            $get_policy_det = $CI->db->query("select initial_cd,cd_threshold,cd_utilised,allow_negative_issuance,threshold_value,(select sum(amount) from cd_deposit cd where cd.partner_id=mc.creditor_id) as deposit  from master_ceditors mc where creditor_id = '$creditor_id'")->row_array();
            $allow_negative_issuance=0;
            if (count($get_policy_det) > 0) {
                $cd_threshold_percent = $get_policy_det['cd_threshold'];
                $initial_cd = $get_policy_det['initial_cd'];
                $allow_negative_issuance = $get_policy_det['allow_negative_issuance'];
                $threshold_value = $get_policy_det['threshold_value'];
                $total_amount = ($amount_deposite);
                if($threshold_value == 1){
                    $cd_threshold = (($total_amount) * ($cd_threshold_percent)) / 100; //percent
                }else{
                    $cd_threshold=$cd_threshold_percent; //amount
                }

                $cd_utilised = $amount_utilzed;
                $balance = ($total_amount) - $cd_utilised;
            }
            $arr = array(
                'amount_utilized' => $amount_utilzed,
                'total_amount' => $total_amount,
                'threshold_amount' => $cd_threshold,
                'balance' => $balance,
                'initial_amount' => $initial_cd,
                'allow_negative_issuance' => $allow_negative_issuance,
                'collection_amount' => $collection_amount,
            );
            if ($balance <= 0) {
                if ($allow_negative_issuance == 1) {
                    $response = array('status' => 200, 'msg' => "NegativeAllow", 'data' => $arr);
                } else {
                    $response = array('status' => 201, 'msg' => "Not Sufficient CD Balance.", 'data' => $arr);
                }
            }else if($balance < $collection_amount){
                if ($allow_negative_issuance == 1) {
                    $response = array('status' => 200, 'msg' => "NegativeAllow", 'data' => $arr);
                } else {
                    $response = array('status' => 201, 'msg' => "Not Sufficient CD Balance.", 'data' => $arr);
                }
            }elseif ($balance <= $cd_threshold) {
                $response = array('status' => 200, 'msg' => "LessCD", 'data' => $arr);
            }else if($balance >= $collection_amount){
                $response = array('status' => 200, 'msg' => "Success", 'data' => $arr);
            } else {
                $response = array('status' => 201, 'msg' => "Not Sufficient CD Balance.", 'data' => $arr);
            }

        }else{
            $response = array('status' => 200, 'msg' => "Payment Mode is not CD balance for this plan.",'data'=>array());
        }

        return $response;
    }
}
if (!function_exists('sendMailCDbalance')) {
    function sendMailCDbalance($ceditor_email,$creaditor_name,$cd_threshold,$balance,$collection_amt,$msg){


        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Username   = 'noreply@elephant.in';                     //SMTP username
            $mail->Password   = 'dpwvzfrtjzmqlvcc';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@elephant.in', 'Mailer');
            // $mail->addAddress('poojalote123@gmail.com', 'Pooja Lote');     //Add a recipient
            //    $mail->addAddress('pooja.lote@fyntune.com', 'Pooja Fyntune');     //Add a recipient
            $mail->addAddress($ceditor_email);
            $mail->addBcc('poojalote123@gmail.com');


            //Add a recipient
            if($msg == "LessCD"){
                $p='<p>Your CD balance has reached its threshold value, as indicated below.</p>';
            } else if($msg == "NegativeAllow"){
                $p='<p>Your CD balance is in negative.</p>';
            }else{
                $p='<p>Your CD balance has reached its threshold value, as indicated below.</p>';
            }
            $body="
        Dear ".$creaditor_name.",<br>

".$p."
<p>Threshold for CD balance :   ".$cd_threshold."</p>
<p>Total CD Balance :   ".$balance."</p>";
if($collection_amount>0){

    $body.="<p>Amount You Requested :   ".$collection_amt."</p>";
}

$body.="<p>Please add CD balance to your account.</p>
Thanks ,<br>
Team Elephant<br>

Please do not reply, this is system generated e-mail.
";
     
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Insufficient CD balance.';
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return 'Message has been sent';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

if (!function_exists('sendMail'))
{
    function sendMail($to,$subject,$body){
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Username   = 'noreply@elephant.in';                     //SMTP username
            $mail->Password   = 'dpwvzfrtjzmqlvcc';                               //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@elephant.in', 'Mailer');
            ///    $mail->addAddress('poojalote123@gmail.com', 'Pooja Lote');     //Add a recipient
            //    $mail->addAddress('pooja.lote@fyntune.com', 'Pooja Fyntune');     //Add a recipient
            $mail->addAddress($to);
           

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

            return json_encode(["status"=>true]);
        } catch (Exception $e) {
            return json_encode(["status"=>false,'message'=>"Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);

        }
    }
}

if(!function_exists('sendSms')){
    function sendSms($sms_to,$sms_text){
          
        
        $postdata = http_build_query(
            array(
                 'feedid'=>'366724',
                 'senderid'=>'ELPHNT',
                 'username'=>'7777001974',
                 'password'=>'Alliance@123',
                 'To'=>$sms_to,
                 'Text'=>urlencode('sms_text'),
                       //'AxisDirect Trading a/c. Our Sales Team___________ (name - Mbl no) will contact you shortly.'
            )
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        
        return $json;

    
    }
}
if(!function_exists('createCommunicationLog')){
    function createCommunicationLog($data){

      
      $CI = &get_instance();    
      $CI->db->insert('communication_logs',$data);
      return $result_id = $CI->db->insert_id();
    
    }
}