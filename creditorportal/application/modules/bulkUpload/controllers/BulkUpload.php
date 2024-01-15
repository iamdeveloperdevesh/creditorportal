<?php
header('Access-Control-Allow-Origin: *');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once PATH_VENDOR.'vendor/autoload.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/Exception.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PATH_VENDOR.'vendor/phpmailer/phpmailer/src/SMTP.php';
use phpseclib3\Net\SFTP;
class BulkUpload extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
		ini_set('upload_max_filesize', '20M');  
		ini_set('post_max_size', '25M');  
	}

	function index()
	{

		$this->load->view('template/header.php');
		$this->load->view('bulkUpload/index');
		$this->load->view('template/footer.php');
	}
	function AddBulkFile()
	{
		/*ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);*/
		$this->load->library('excel');
		$is_sftp=$this->input->post('is_sftp');
		if($is_sftp == 1){
			$sftp = new SFTP('healthrenewalnotices.blob.core.windows.net');
			$sftp->login('healthrenewalnotices.nobrokers', 'BD//+EtWR6u2P4CeTHCzTpRgzPEq7nc5');
			$dir2='Coi Document PDF';
			$sftp->chdir($dir2);
		}

		if (isset($_FILES["uploadfile"]["name"])) {
			$data['testing'] = "dfdfdsfd";
			$path = $_FILES["uploadfile"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);
			$worksheet = $object->getSheet(0);
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			$rowDatafirst = $worksheet->rangeToArray('A1:' . $highestColumn . '1',
				null, true, false);
			/*$data_array=array('Invoice No (Unique identification field)','Invoice Date','Proposal number','Proposer fisrt name Name','Address1','Address2','Address3',
				'Pincode','Request Type','Mode of Shipment','From-City','To-City','Cargo value','Expected date of shipment',
				'Place of Issuance','Subject Matter Insured','Customer mail id','Customer mobile no.','Coi number','Coi url','issuance date');*/
			$data_array=array('Unique_ID',
				'LAN No.','Loan disburement date','Master policy number','Proposal No.- System','Proposal number','Plan Name','Coi number','COI URL',
				'Policy start date','Policy end date'
			);

		$difference_array=	array_merge(array_diff($data_array,$rowDatafirst[0]),array_diff($rowDatafirst[0],$data_array));
			if(count($difference_array) == 0){
				for ($row = 1; $row <= $highestRow; $row++) {
					$rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
						null, true, false);
					$rowData=$rowData[0];

					if($row != 1){
						$proposal_number=$rowData[4];
						$proposal_number_ic=$rowData[5];
						$invoice_number=$rowData[1];
						$master_policy_number_product=$rowData[3];
						$lan_number=$rowData[1];
						$_POST['created_by']=1;
						$_POST['invoice_number']=$invoice_number;

						$lead_id_que=$this->db->query("select lead_id,trace_id from lead_details where unique_id='".$rowData[0]."'")->row();
						$lead_id=$lead_id_que->lead_id;
						$trace_id=$lead_id_que->trace_id;
						$policy_id=$this->db->query("select policy_id from master_policy where policy_number='".$master_policy_number_product."'")->row()->policy_id;
//						$trace_id=$this->db->query("select trace_id from lead_details where lead_id='".$lead_id."'")->row()->trace_id;
						$ClientCreation['lead_id']=$lead_id;
						$ClientCreation['trace_id']=$trace_id;
						//check LeadID Exist or not
						$checkLeadExistorNot = curlFunction(SERVICE_URL . '/customer_api/checkLeadExistorNot', array('lead_id' => $lead_id, 'trace_id' => $trace_id,'unique_id'=>$rowData[0]));
						//var_dump($checkLeadExistorNot);die;
						if ($checkLeadExistorNot) {
							$lan_dis_date=$rowData[2];
							$start_date=$rowData[9];
							$end_date=$rowData[10];
							//$proposer_name=$rowData[3];
							$coi_number=$rowData[7];
							//$coi_url=$rowData[19];
							//$customer_mail_id=$rowData[16];
							if($is_sftp == 1){
								$customer_mail_id=$rowData[16];
								$f_name=$coi_number.'.pdf';
								$sftp->get($f_name, FCPATH."/assets/marine_coi/".$f_name);
								$coi_url="/assets/marine_coi/".$f_name;
								$check_already_updated=$this->db->query("select lead_id from api_proposal_response where 
                                    lead_id=".$lead_id." AND proposal_no='".$proposal_number."' AND coi_mail_sent=1 AND certificate_number !=''");
								if($this->db->affected_rows() > 0){
									$result='Already Updated';
									//insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
								}else{
									$update=false;
									$mail_array=array($customer_mail_id,$proposer_name,$coi_url,$proposal_number);
									$sendMail=$this->sendMail($mail_array);
									if($sendMail == 200){
										$where=array('lead_id'=>$lead_id,'proposal_no'=>$proposal_number);
										$this->db->where($where);
										$update=$this->db->update('api_proposal_response',array('COI_url'=>$coi_url,'certificate_number'=>$coi_number,'coi_mail_sent'=>1));
									}
									if($update == true){
										$result='Updated';
										insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
									}else{
										$result='Not Updated';
										insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
									}
								}
							}else{
								$coi_url=$rowData[8];
								$where=array('lead_id'=>$lead_id,'master_policy_id'=>$policy_id);
								$this->db->where($where);
								$update=$this->db->update('api_proposal_response',array('certificate_number'=>$coi_number,
									'start_date'=>date('Y-m-d H:i:s',strtotime($start_date)),
									'end_date'=>date('Y-m-d H:i:s',strtotime($end_date)),
									'ProposalNumber'=>$proposal_number_ic,
									'COI_url'=>$coi_url,
								));
								//echo $this->db->last_query();
								if($update == true){
									$result='Updated';
									insert_application_log($lead_id, "coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
								}else{
									$result='Not Updated';
									insert_application_log($lead_id, "coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
								}
							}

						} else {
							$result="Customer Not found.";

							//  insert_application_log($lead_id, "marine_coi_data_insert", json_encode($_POST), $result, $_POST['created_by']);
						}
					}
				}
				//exit;
				$response['msg']="Successfully updated check logs for more details.";
				$response['success']=true;
				echo json_encode($response);
			}else{
				$response['msg']="Wrong file Uploaded";
				$response['success']=false;
				echo json_encode($response);
			}

		}else{
			$response['msg']="Please Upload File";
			$response['success']=false;
			echo json_encode($response);
		}
	}
	function sendMail($mail_array){



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
			$mail->addAddress($mail_array[0]);
			$url=base_url().$mail_array[2];
			//Add a recipient

			/*     $body="<p>Hello,</p>
         <p>Please click on below given link to get your certificate of Issuance.</p>
         <p>".$url."</p>
         <p>Regards,<br>
         Fyntune Team.
         </p>
         ";*/
			$body="
        Dear ".$mail_array[1].",<br>
Your insurance certificate is Issued, please click on below link to download your Insurance certificate.<br>
".$url."<br>
Thanks ,<br>
Team Elephant<br>

Please do not reply, this is system generated e-mail.
";
			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Certificate Issuance';
			$mail->Body    = $body;
			// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			return 200;
		} catch (Exception $e) {
			return 201;
		}
	}
	function addProposaldocument(){
		//print_r($_FILES["coiuploadfile"]["name"]);die;
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		if(count($_FILES["coiuploadfile"]["name"]) > 0){
			$cnt_updated=0;
			$cnt_not_updated=0;
			$i=0;
			foreach ($_FILES["coiuploadfile"]["name"] as $name){
				$info = pathinfo($name);
				$filename=$info['filename'];
				$filenamewithext=$info['basename'];
				$check_coi_number_updated=$this->db->query("select proposal_no from api_proposal_response where certificate_number='".$filename."'");
				if($this->db->affected_rows() > 0){
					$proposal_number=$check_coi_number_updated->row()->proposal_no;
					$target = FCPATH."/assets/marine_coi/".$filenamewithext;
					if (file_exists($target)) {
						unlink($target);
					}
					move_uploaded_file( $_FILES['coiuploadfile']['tmp_name'][$i], $target);
					$filepath="/assets/marine_coi/".$filenamewithext;
					$where=array('proposal_no'=>$proposal_number);
					$this->db->where($where);
					$update=$this->db->update('api_proposal_response',array('COI_url'=>$filepath));
					if($update){
						$cnt_updated++;
					}else{
						$cnt_not_updated++;
					}
				}
				$i++;
			}
			if($cnt_updated > 0){
				$response['msg']=$cnt_updated. " File(s) uploaded Successfully.";
				$response['success']=true;
				echo json_encode($response);
			}else{
				$response['msg']="Something went wrong.";
				$response['success']=false;
				echo json_encode($response);
			}
		}else{
			$response['msg']="Please Upload File";
			$response['success']=false;
			echo json_encode($response);
		}


	}


}

?>
