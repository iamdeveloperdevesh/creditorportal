<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cron_m extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->model("ABC/ABC_m", "Abc_m");
    }

    function getSmsDeliveryStatus() {
        $pending_sms = $this->db
                ->select('transaction_id')
                ->from('sms_log')
                ->where('status', 'P')
                ->where('log', '')
                ->get()
                ->result_array();
        if ($pending_sms) {
            $this->load->library('smslib');
            foreach ($pending_sms as $k => $v) {
                $pending_sms[$k] = $v['transaction_id'];
            }
            $pending_ids = array_chunk($pending_sms, 5);
            foreach ($pending_ids as $pending) {
                $str = implode(',', $pending);
                $response = rtrim($this->smslib->getDeliveryReportTransId($str), ',');
                if ($response != '') {
                    $resp = explode(',', $response);
                    for ($i = 0; $i < count($resp); $i++) {
                        if ($resp[$i] == 'Delivered') {
                            $this->db->where('transaction_id', $pending[$i]);
                            $this->db->update('sms_log', ['status' => 'D']);
                        }
                    }
                }
            }
        }
    }
	
	function docServiceCal($postField, $saveField) {
		
		$this->db->insert("logs_docs", [
			"req" => json_encode($saveField),
			"lead_id" => $saveField['UploadRequest'][0]['DataClassParam'][0]['Value'],
			"type" => "OmniDocs"
		]);
		
		$id = $this->db->insert_id();
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/uploadRequest",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($postField),
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"password: esb@axis",
			"postman-token: a3f0ed2e-f9cc-f767-09ae-4c594e38d5f2",
			"username: esb_axis"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$this->db->where([
				"id" => $id
			])->update("logs_docs", [
				"res" => json_encode($err),
				"type" => "OmniDocs"
			]);
			// Monolog::saveLog("omniDocResError", "I", $err);
		  // echo "cURL Error #:" . $err;
		  
		} else {
			$this->db->where([
				"id" => $id
			])->update("logs_docs", [
				"res" => json_encode($response),
				"type" => "OmniDocs"
			]);
			// Monolog::saveLog("omniDocRes", "I", $response);
		  // echo $response;
		}
	}

    function send_messages() {
        set_time_limit(0);
        $queue_records = $this->db
                ->get_where('queue_log', ['status' => '0', 'type' => 'S'])
                ->result_array();
        //print_r($queue_records); die();
        $this->load->library('smslib');

        foreach ($queue_records as $record) {
            $content = json_decode($record['content'], true);
            //print_pre($content); die();
            $data = [];
            switch ($content['template']['module_name']) {
                case 'NETWORK_HOSPITAL':
                    $data = [
                        $content['send_details']['name'],
                        $content['send_details']['adddress'],
                    ];
                    break;
                case 'INTIMATECLAIM':
                    $data = [
                        $content['send_details']['Name'],
                        $content['send_details']['REF'],
                    ];
                    break;
                case 'REGISTRATION':
                    $data = [
                        $content['send_details']['first_name'],
                    ];
                    break;
                case 'UPLOAD_POLICY_SUCCESS':
                    $data = [
                        $content['send_details']['first_name'],
                        $content['send_details']['section'],
                    ];
                    break;
            }
            // $content['send_details']['mobile_no'] = '8805685311';
            $trans_details = $this->smslib->sendTransactionalSms(
                    $content['template']['template_id'], $content['send_details']['mobile_no'], $content['template']['template'], $data
            );
            //exit;
            $this->db->reconnect();
            $this->smslib->insertSmsLog([
                'name' => $content['send_details']['first_name'] . ' ' . $content['send_details']['last_name'],
                'mobile_no' => $content['send_details']['mobile_no'],
                'message' => $trans_details['message'],
                'transaction_id' => $trans_details['transaction_id'],
            ]);
            $this->db->reconnect();
            $this->db->where('queue_id', $record['queue_id']);
            $this->db->update('queue_log', ['status' => '1', 'sent_at' => date('Y-m-d H:i:s')]);
        }
    }

    function send_emails() {
        set_time_limit(0);
        $queue_records = $this->db
                ->get_where('queue_log', ['status' => '0', 'type' => 'E'])
                ->result_array();
				//print_Pre($queue_records);exit;
        $this->load->library('email');
        foreach ($queue_records as $record) {
            //print_r($record);
            $content = json_decode($record['content'], true); //die();
            //echo $content['message'];
            $user_mail_data = [
                'to' => $content['to'],
                'subject' => $content['subject'],
                'message' => $content['message'],
            ];
            //$user_mail_data['reply_to']['email'] = "sahil.fyntune@gmail.com";
            //$user_mail_data['reply_to']['name'] = "sahil";
            //print_pre(Email::sendMail($user_mail_data));exit;
            if (Email::sendMail($user_mail_data)) {
                $this->db->reconnect();
                $this->db->where('queue_id', $record['queue_id']);
                $this->db->update('queue_log', ['status' => '1', 'sent_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    function send_leads() {
        set_time_limit(0);
        $queue_records = $this->db
                ->get_where('queue_log', ['status' => '0', 'type' => 'L'])
                ->result_array();
        foreach ($queue_records as $record) {
            $content = json_decode($record['content'], true);
            $response = save_lead_deal($content);
            if (!empty($response)) {
                $this->db->reconnect();
                $this->db->where('queue_id', $record['queue_id']);
                $this->db->update('queue_log', ['status' => '1', 'sent_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    function send_policy_reminder() {

        $records = $this->db->select('*')
                ->from('user_policy_uploads as upu')
                ->join('users as u', 'upu.user_id = u.user_id', 'left')
                ->where('expiry_date = CURDATE() + INTERVAL 7 DAY')
                ->or_where('expiry_date = CURDATE() + INTERVAL 30 DAY')
                ->where('expiry_reminder', 'Y')
                ->get()
                ->result_array();
        foreach ($records as $rec) {
            if ($rec['email'] != '') {
                if (file_exists(APPPATH . 'resources/uploads/user_policy_documents/' . $rec['policy_document'])) {
                    $mail_data['attachment'] = APPPATH . 'resources/uploads/user_policy_documents/' . $rec['policy_document'];
                }
                //code for PM members
                $data = [
                    'name' => $rec['first_name'] . ' ' . $rec['last_name'],
                    'mobile_no' => $rec['mobile_no'],
                    'email' => $rec['email'],
                    'insu_type' => $rec['insu_type'],
                    'pre_pol_comp' => $rec['insurer_name'],
                    'expiry_date' => format_datetime($rec['expiry_date'], 'D'),
                    'send_to' => 'PM'
                ];
                $mail_data['to'] = SUPPORT;
                $mail_data['cc'] = COPYTO;
                $mail_data['subject'] = 'Policy Expiration Reminder of ' . $data['name'] . ' (' . $rec['insu_type'] . ' Insurance)';
                $mail_data['message'] = $this->load->view('email_new', ['subview' => send_policy_reminder_template($data), 'name' => 'Director'], TRUE);
                sendMail($mail_data);
                sleep(3);

                //code for client
                $data['send_to'] = 'CLIENT';
                $mail_data['to'] = $rec['email'];
                $mail_data['subject'] = "Policy Expiration Reminder($rec[insu_type] Insurance)";
                $mail_data['message'] = $this->load->view('email_new', ['subview' => send_policy_reminder_template($data), 'name' => $rec['first_name'] . ' ' . $rec['last_name']], TRUE);
                sendMail($mail_data);
                sleep(3);
            }
        }
    }

    function send_daily_newsletter() {
        $data = $this->db->select('*')
                ->from('blogs')
                ->where('DATE(published_at) = "' . date('Y-m-d', strtotime('-1 day')) . '"')
                ->where('status', STATUS_PUBLISHED)
                ->where('type', TYPE_NEWS)
                ->order_by('published_at', 'DESC')
                ->get()
                ->result_array();

        if (count($data) > 0) {
            $identifier = 'Daily Newsletter - ' . date('M j, Y');
            $subject = BROKER_NAME . ' Daily Newsletter - ' . date('M j, Y');
            $message = $this->load->view('email_blogs', [
                'pre_header' => 'News which can be of use | Stay updated about what\'s trending in insurance industry | ' . $data[0]['title'],
                'subview' => send_daily_newsletter($data)
                    ], TRUE);
            $this->send_newsletter($subject, $message, $identifier);
        }
    }

    function send_weekly_newsletter() {
        $data = $this->db->select('*')
                ->from('blogs')
                ->where('DATE(published_at) BETWEEN "' . date('Y-m-d', strtotime('-7 day')) . '" AND "' . date('Y-m-d') . '"', NULL, FALSE)
                ->where('status', STATUS_PUBLISHED)
                ->where('type', TYPE_NEWS)
                ->order_by('published_at', 'DESC')
                ->get()
                ->result_array();

        if (count($data) > 0) {
            $identifier = 'Weekly Newsletter (' . date('M j', strtotime('-7 day')) . ' - ' . date('M j') . ')';
            $subject = BROKER_NAME . ' Weekly Newsletter (' . date('M j', strtotime('-7 day')) . ' - ' . date('M j') . ')';
            $message = $this->load->view('email_blogs', [
                'pre_header' => 'Seek news of the week | Stay updated about what\'s trending in insurance industry | ' . $data[0]['title'],
                'subview' => send_weekly_newsletter($data)
                    ], TRUE);
            $this->send_newsletter($subject, $message, $identifier);
        }
    }

    function send_weekly_articles() {
        $data = $this->db->select('*')
                ->from('blogs')
                ->where('DATE(published_at) BETWEEN "' . date('Y-m-d', strtotime('-6 day')) . '" AND "' . date('Y-m-d') . '"', NULL, FALSE)
                ->where('status', STATUS_PUBLISHED)
                ->where('type', TYPE_ARTICLE)
                ->order_by('published_at', 'DESC')
                ->get()
                ->result_array();

        if (count($data) > 0) {
            $identifier = 'Weekly Articles (' . date('M j', strtotime('-6 day')) . ' - ' . date('M j') . ')';
            $subject = BROKER_NAME . ' Weekly Articles (' . date('M j', strtotime('-6 day')) . ' - ' . date('M j') . ')';
            $message = $this->load->view('email_blogs', [
                'pre_header' => 'A word from our experts to save your time and money | Get all the information you need | ' . $data[0]['title'],
                'subview' => send_weekly_articles($data)
                    ], TRUE);
            $this->send_newsletter($subject, $message, $identifier);
        }
    }

    function send_newsletter($subject, $message, $identifier) {
        $subscriber_data = $this->db->select('email')->get_where('blog_subscribers', ['activated' => 'Y'])->result_array();

        $subscriber_data = array_map(function ($sub) {
            return $sub['email'];
        }, $subscriber_data);

        $subscriber_data_chunks = array_chunk($subscriber_data, 1000);

        //$this->load->library('pepipost_sendmail');
        $this->load->library('sparkpost_sendmail');

        foreach ($subscriber_data_chunks as $subscribers) {
            $user_mail_data = [
                'identifier' => $identifier,
                'to' => $subscribers,
                'reply_to' => MEDIA,
                'subject' => $subject,
                'message' => $message
            ];

            //$this->pepipost_sendmail->send_mail($user_mail_data);
            $this->sparkpost_sendmail->send_mail($user_mail_data);
        }
    }

    function clear_all_dumps() {
        // Clear preview data
        /* $get_image_data = $this->db->get('blogs_preview')->result_array();
          foreach ($get_image_data as $image_data) {
          @unlink(APPPATH . 'resources/blogs/' . $image_data['banner_image']);
          }
          $this->db->truncate('blogs_preview');
         */

        // Close log in sessions
        $date = date('Y-m-d');
        //$datetime = date('Y-m-d H:i:s');
        /* $this->db->simple_query("UPDATE user_sessions SET
          session_stop = '" . strtotime($datetime) . "',
          total_session_timeout = ('" . strtotime($datetime) . "' - session_start),
          is_disconnected = 'Y'
          WHERE session_start <= '" . strtotime($date) . "'
          AND is_disconnected = 'N'"); */

        // Move quote to dump if quote datetime+30 days < today
        $this->db->simple_query("INSERT INTO quote_log_dump SELECT * FROM quote_log WHERE DATE_ADD(DATE(searched_at),INTERVAL 30 DAY) < '" . $date . "'");
        $this->db->simple_query("DELETE FROM quote_log WHERE DATE_ADD(DATE(searched_at),INTERVAL 30 DAY) < '" . $date . "'");

        delete_files('application/logs/car_quotes/', true, true);
        delete_files('application/logs/bike_quotes/', true, true);
        delete_files('application/logs/mediclaim_quotes/', true, true);
        delete_files('application/ci_sessions/', true, true);
        delete_files('application/cache/', true, true);
        delete_files('public/assets_min/css/', true, true);
        delete_files('public/assets_min/js/', true, true);
    }

    function fetch_mmv_bajaj_allianz() {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $array = [
            'getAllVehicleMaster' => [
                'pUserId' => AUTH_NAME_BAJAJ_ALLIANZ_MOTOR,
                'pPassword' => AUTH_PASS_BAJAJ_ALLIANZ_MOTOR,
                'pProductCode' => PRODUCT_CODE_BAJAJ_ALLIANZ_MOTOR,
                'pVehicleMasterList_out' => '',
                'pError_out' => '',
                'pErrorCode_out' => '',
                '@attributes' => [
                    'xmlns' => 'http://com/bajajallianz/motWebPolicy/BjazGetVehicleMaster.wsdl',
                ],
            ],
        ];

        $this->load->helper('web_service');

        $data = get_ws_data(
                END_POINT_URL_VEHICLE_MASTER_BAJAJ_ALLIANZ_MOTOR, $array, 'bajaj_allianz', [
            'root_tag' => 'Body',
            'container' => '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">#replace</Envelope>',
                ]
        );

        if (!empty($data)) {
            try {
                $return_data = XML2Array::createArray(remove_xml_namespace($data));

                $return_data = $return_data['Envelope']['Body']['getAllVehicleMasterResponse'];

                if ($return_data['pErrorCode_out'] == '0') {
                    $this->db->trans_begin();

                    $this->db->truncate('bajaj_allianz_model_master');

                    $return_data = $return_data['pVehicleMasterList_out']['BjazWsVehicleMasterObjUser'];

                    $chunk_data = array_chunk($return_data, 25);

                    foreach ($chunk_data as $chunk) {
                        $insert_array = [];

                        foreach ($chunk as $item) {
                            $insert_array[] = [
                                'vehicle_code' => $item['vehicleCode'],
                                'vehicle_type' => $item['vehicleType'],
                                'vehicle_make_code' => $item['vehicleMakeCode'],
                                'vehicle_make' => $item['vehicleMake'],
                                'vehicle_model_code' => $item['vehicleModelCode'],
                                'vehicle_model' => $item['vehicleModel'],
                                'vehicle_subtype_code' => $item['vehicleSubtypeCode'],
                                'vehicle_subtype' => $item['vehicleSubtype'],
                                'fuel' => $item['fuel'],
                                'cubic_capacity' => $item['cubicCapacity'],
                                'carrying_capacity' => $item['carryingCapacity'],
                            ];
                        }

                        $this->db->insert_batch('bajaj_allianz_model_master', $insert_array);
                    }

                    if ($this->db->trans_status() === false) {
                        $this->db->trans_rollback();
                    } else {
                        $this->db->trans_commit();

                        /* $this->load->library('excel');

                          $objPHPExcel = new PHPExcel();

                          try
                          {
                          $objPHPExcel->getActiveSheet()
                          ->fromArray($return_data, null, 'A1');
                          $objPHPExcel->getActiveSheet()
                          ->setTitle('MMV MASTER');
                          $objPHPExcel->getActiveSheet()
                          ->getColumnDimension('A')
                          ->setAutoSize(true);
                          $objPHPExcel->getActiveSheet()
                          ->getColumnDimension('B')
                          ->setAutoSize(true);

                          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

                          $filename = APPPATH . 'resources/download/car_mmv_master/bajaj-' . date('d-m-Y') . '.xlsx';

                          $objWriter->save($filename);

                          /*$this->load->library('sparkpost_sendmail');

                          $user_mail_data = [
                          'to'      => 'atish.a@fyntune.com',
                          //'cc'      => 'atish.a@fyntune.com',
                          'subject' => 'Bajaj Allianz MMV - ' . date('d-m-Y'),
                          'message' => '<p>Download Master from here. <a href="' . base_url($filename) . '">Click Here to Download</a></p>',
                          ];

                          $this->sparkpost_sendmail->send_mail($user_mail_data);
                          }
                          catch (PHPExcel_Exception $e)
                          {
                          die();
                          } */
                    }
                }
            } catch (Exception $e) {
                
            }
        }
    }

    function get_paramount_data($envelope) {
        $uat = 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/GeFamilyEnrollmentDetails';
        $this->load->library("paramount");
        $data = $this->paramount->getdata($envelope, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/GeFamilyEnrollmentDetails', array(
            "Content-Type: application/json",
            "cache-control: no-cache"
        ));
        $aa = json_decode($data["GeFamilyEnrollmentDetailsResult"], true);
        if (isset($aa["Table"])) {
            return $aa["Table"];
        }
    }

    function paramount($data) {
		

        $array = [
            "USERNAME" => "TATA-MOTORS",
            "PASSWORD" => "ADMIN@123",
            "EMPLOYEE_NO" => $data["emp_code"],
            "POLICY_NO" => $data["policy_no"]
        ];

        $envelope = json_encode($array);
		//print_pre($envelope);exit;
        $result = $this->get_paramount_data($envelope);
		//print_pre($result);exit;
        //print_pre($result);
        //$data["emp_id"] = 1;
        //print_pre($result);
        if ($result) {

            $subQuery1 = $this->db
                    ->select('ed.emp_code,epm.tpa_member_name,"self" as "fr_name", epd.policy_no,epm.policy_member_id,ed.bdate')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_details as ed')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id', 0)
					->where('fr.emp_id = ed.emp_id')
                    ->where('fr.emp_id', $data["emp_id"])
                   // ->where('ed.fr_id = mfr.fr_id')
                    //->where('fr.emp_id = ed.fr_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->group_by('epm.policy_member_id')
                    ->get_compiled_select();
            $subQuery2 = $this->db
                    ->select('ed.emp_code,epm.tpa_member_name,mfr.fr_name, epd.policy_no,epm.policy_member_id,efd.family_dob')
                    ->from('employee_details as ed,employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_family_details as efd, master_family_relation as mfr')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id = efd.family_id')
                    ->where('efd.fr_id = mfr.fr_id')
                    ->where('fr.emp_id = ed.emp_id')
                   ->where('ed.emp_id', $data['emp_id'])
                    //->where('efd.fr_id = mfr.fr_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->get_compiled_select();
			//print_pre($subQuery1 . ' UNION ' . $subQuery2);exit;
            $op = $this->db->query($subQuery1 . ' UNION ' . $subQuery2)->result_array();
			//print_pre($op);
			//print_pre($result);exit;
            for ($j = 0; $j < count($result); $j++) {
                for ($k = 0; $k < count($op); $k++) {


                    if (strtolower($result[$j]["RELATIONSHIP"]) == "employee") {
                        $result[$j]["RELATIONSHIP"] = "SELF";
                    }

                    if (strtolower($result[$j]["RELATIONSHIP"]) == "wife" || strtolower($result[$j]["RELATIONSHIP"]) == "husband") {
                        $result[$j]["RELATIONSHIP"] = "Spouse/Partner";
                    }
                     //print_Pre(strtolower($result[$j]["RELATIONSHIP"])." ==". strtolower($op[$k]["fr_name"]));
                     //echo "<br>";
                    // print_Pre(date("d-m-Y", strtotime($result[$j]["DOB"]))." ==".$op[$k]["bdate"]);

                    if (strtolower($result[$j]["RELATIONSHIP"]) == strtolower($op[$k]["fr_name"]) && (date("d-m-Y", strtotime($result[$j]["DOB"])) == $op[$k]["bdate"])) {
                       
                        $this->db->where(["policy_member_id" => $op[$k]["policy_member_id"]])->update("employee_policy_member", ["tpa_member_id" => $result[$j]["TPA_ID"], "tpa_member_name" => $result[$j]["MEMBERNAME"]]);
						//echo $this->db->last_query();exit;
						 $insert_data = [
                            "MemberUHIDNo" => $result[$j]["TPA_ID"],
                            "enrollment_details" => json_encode($result[$j])
                        ];
                        $this->db->insert('tpa_enrollment_data', $insert_data);
                        
                    }
                }
            }
        }
    }

    function get_paramount_network_hospitals($envelope) {

        $this->load->library("paramount");
        $data = $this->paramount->getdata($envelope, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/GetHospitalList', array(
            "Content-Type: application/json",
            "cache-control: no-cache"
        ));
        $aa = json_decode($data["GetHospitalListResult"], true);

        return $aa;
    }

    function paramount_network_hospital($data) {
        $array = [
            "USERNAME" => "TATA-MOTORS",
            "PASSWORD" => "ADMIN@123",
            "POLICY_NO" => $data["policy_no"]
        ];

        $envelope = json_encode($array);
        $result = $this->get_paramount_network_hospitals($envelope);

        if ($result) {
            $network = [];
            for ($i = 0; $i < count($result); $i++) {
                $network[$i] = [
                    "policy_no" => $data["policy_no"],
                    "HOSIDNO1" => $result[$i]["Provider Number"],
                    "ADDRESS1" => @$result[$i]["Address 1"] . " " . @$result[$i]["Address 2"] . " " . @$result[$i]["Address Area"],
                    "HOSPITAL_NAME" => $result[$i]["Provider Name"],
                    "PIN_CODE" => $result[$i]["Pin Code"],
                    "PHONE_NO" => $result[$i]["Telephone Number"],
                    "CITY_NAME" => $result[$i]["CITY"],
                    "STATE_NAME" => $result[$i]["STATE"],
                    "TPA_ID" => "2"
                ];
            }
            $this->db->trans_start();
            $this->db->insert_batch('network_hospitals', $network);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                # Something went wrong.
                $this->db->trans_rollback();
                return FALSE;
            } else {
                # Everything is Perfect. 
                # Committing data to the database.
                $this->db->trans_commit();
                return TRUE;
            }
        }
    }

///mediassist
    function get_tpa_member_id($envelope) {

        $this->load->library("soaprequest");
        try {
            $data = $this->soaprequest->getdata($envelope, 'https://integration.medibuddy.in/TataMotorsAPI/soap11', array(
                "Content-Type: text/xml",
                "SOAPAction: EnrollmentDataRequest",
                "cache-control: no-cache"
            ));
        } catch (Exception $e) {
            $data = '';
        }
        return $data;
    }

    function mediast($data) {

        //print_pre($data);exit;
        $envelope = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>

<EnrollmentDataRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
  <EmployeeCode>' . $data["emp_code"] . '</EmployeeCode>
  <Password>TM3FA5g</Password>
    <PolicyNo>' . $data["policy_no"] . '</PolicyNo>
  <UserName>TataMotors</UserName>
</EnrollmentDataRequest>

    </soap:Body>
</soap:Envelope>';
        try {
            $update_data = $this->get_tpa_member_id($envelope);
            //print_pre($update_data); exit;
            $result = $update_data['sBody']['EnrollmentDataResponse']['EnrolmentData']['EnrolmentData'];

            $subQuery1 = $this->db
                    ->select('"self" as "fr_name", epd.policy_no,epm.policy_member_id,ed.bdate')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_details as ed')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id', 0)
                    ->where('fr.emp_id', $data["emp_id"])
                 //   ->where('ed.fr_id = mfr.fr_id')
                    ->where('fr.emp_id = ed.emp_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->group_by('epm.policy_member_id')
                    ->get_compiled_select();
            $subQuery2 = $this->db
                    ->select('mfr.fr_name, epd.policy_no,epm.policy_member_id,efd.family_dob')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_family_details as efd, master_family_relation as mfr')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id = efd.family_id')
                    ->where('efd.fr_id=mfr.fr_id')
                    ->where('fr.emp_id', $data['emp_id'])
                    ->where('efd.fr_id = mfr.fr_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->get_compiled_select();

            $op = $this->db->query($subQuery1 . ' UNION ' . $subQuery2)->result_array();

            // print_pre(count($result)); exit;print_pre($result);echo "<br>";
            //print_pre($op); exit;

            for ($j = 0; $j < count($result); $j++) {
                for ($k = 0; $k < count($op); $k++) {
                    print_Pre(strtolower($result[$j]["MemberRelation"]) . " ==" . strtolower($op[$k]["fr_name"]));
                    if (count($result) == 10) {
                        if (strtolower($result["MemberRelation"]) == strtolower($op[$k]["fr_name"])) {
                            $this->db->where(["policy_member_id" => $op[$k]["policy_member_id"]])->update("employee_policy_member", ["tpa_member_id" => $result["TpaMemberId"]]);
                        }
                    } else {
                        if (strtolower($result[$j]["MemberRelation"]) == strtolower($op[$k]["fr_name"])) {
                            $this->db->where(["policy_member_id" => $op[$k]["policy_member_id"]])->update("employee_policy_member", ["tpa_member_id" => $result[$j]["TpaMemberId"]]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return '';
        }
    }

    function mediast_network_hospital($data) {

        $envelope = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>

<NetworkHospitalRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
  <EndIndex>0</EndIndex>
  <Password>TM3FA5g</Password>
  <PolicyNo>' . $data['policy_no'] . '</PolicyNo>
  <PullOnlyCount>false</PullOnlyCount>
  <StartIndex>0</StartIndex>
  <UserName>TataMotors</UserName>
</NetworkHospitalRequest>

    </soap:Body>
</soap:Envelope>';

        /* $envelope = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
          <Body>
          <getNetworkHospitals xmlns="http://www.mediassistindia.net/">
          <UserName>TataMotors</UserName>
          <Password>TM3FA5g</Password>
          <startIndex>0</startIndex>
          <endIndex>1000</endIndex>
          </getNetworkHospitals>
          </Body>
          </Envelope>'; */

        $network_hospitals = $this->mediassist_network_hospitals($envelope)['sBody']['NetworkHospitalResponse']['ProviderData']['Provider'];
        //print_pre($network_hospitals);exit;
        for ($i = 0; $i < count($network_hospitals); $i++) {
            $data1[] = [
                'HOSIDNO1' => ($network_hospitals[$i]['HOSIDNO1']) ? $network_hospitals[$i]['HOSIDNO1'] : '',
                'policy_no' => ($data['policy_no']) ? $data['policy_no'] : '',
                'PARTNER_ID' => ($network_hospitals[$i]['PARTNER_ID']) ? $network_hospitals[$i]['PARTNER_ID'] : '',
                'ZONE_NAME' => ($network_hospitals[$i]['ZONE_NAME']) ? $network_hospitals[$i]['ZONE_NAME'] : '',
                'HOSPITAL_NAME' => ($network_hospitals[$i]['HOSPITAL_NAME']) ? $network_hospitals[$i]['HOSPITAL_NAME'] : '',
                'ADDRESS1' => ($network_hospitals[$i]['ADDRESS1']) ? $network_hospitals[$i]['ADDRESS1'] : '',
                //'ADDRESS2' => ($network_hospitals[$i]['ADDRESS2']) ? $network_hospitals[$i]['ADDRESS2'] : '',
                'CITY_NAME' => ($network_hospitals[$i]['CITY_NAME']) ? $network_hospitals[$i]['CITY_NAME'] : '',
                'STATE_NAME' => ($network_hospitals[$i]['STATE_NAME']) ? $network_hospitals[$i]['STATE_NAME'] : '',
                'PIN_CODE' => ($network_hospitals[$i]['PIN_CODE']) ? $network_hospitals[$i]['PIN_CODE'] : '',
                'LANDMARK_1' => ($network_hospitals[$i]['LANDMARK_1']) ? $network_hospitals[$i]['LANDMARK_1'] : '',
                'LANDMARK_2' => ($network_hospitals[$i]['LANDMARK_2']) ? $network_hospitals[$i]['LANDMARK_2'] : '',
                'PHONE_NO' => ($network_hospitals[$i]['PHONE_NO']) ? $network_hospitals[$i]['PHONE_NO'] : '',
                'EMAIL' => ($network_hospitals[$i]['EMAIL']) ? $network_hospitals[$i]['EMAIL'] : '',
                'LEVEL_OF_CARE' => ($network_hospitals[$i]['LEVEL_OF_CARE']) ? $network_hospitals[$i]['LEVEL_OF_CARE'] : '',
                'ISHOSPITALACTIVE' => ($network_hospitals[$i]['ISHOSPITALACTIVE']) ? $network_hospitals[$i]['ISHOSPITALACTIVE'] : '',
                'Insurance_Company' => ($network_hospitals[$i]['Insurance_Company']) ? $network_hospitals[$i]['Insurance_Company'] : '',
                'HOSP_CREATED_ON' => ($network_hospitals[$i]['HOSP_CREATED_ON']) ? $network_hospitals[$i]['HOSP_CREATED_ON'] : '',
                'HOSP_MODIFIED_ON ' => ($network_hospitals[$i]['HOSP_MODIFIED_ON']) ? $network_hospitals[$i]['HOSP_MODIFIED_ON'] : '',
                "TPA_ID" => "1"
            ];
        }
        // print_Pre($data1);
//        exit;
        $this->db->insert_batch('network_hospitals', $data1);
    }

    public function mediassist_network_hospitals($envelope) {
        extract($this->input->post());
        $this->load->library("soaprequest");
        // return $this->soaprequest->getdata($envelope);
        try {
            // print_pre($envelope); exit;
            $data = $this->soaprequest->getdata($envelope, 'https://integration.medibuddy.in/TataMotorsAPI/soap11', array(
                "Content-Type: text/xml",
                "SOAPAction: NetworkHospitalRequest",
                "cache-control: no-cache"
            ));
        } catch (Exception $e) {
            $data = '';
        }

        return $data;
    }

    public function mediast_submit_claims($data) {
        $data_all_file = $this->db->select('ecd.claim_doc_medical_bill_path')
                        ->from('employee_claim_reimb as ecr,employee_claim_documents as ecd')
                        ->where('ecr.claim_reimb_id', $data['claim_reimb_id'])
                        ->where('ecd.claim_reimb_id = ecr.claim_reimb_id')
                        ->get()->result_array();

        $data_all = $this->db->select('*')
                        ->from('employee_claim_reimb as ecr,employee_claim_reimb_hospitalization as ecrh')
                        ->where('ecr.claim_reimb_id', $data['claim_reimb_id'])
                        ->where('ecrh.claim_reimb_id = ecr.claim_reimb_id')
                        ->group_by('ecr.claim_reimb_id')
                        ->get()->row_array();


//        $data_all_bill_details = $this->db->select('*')
//                        ->from('employee_claim_reimb_hospitalization as ecrh,employee_reimb_bills as erb,employee_claim_reimb as ecr')
//                        ->where('ecrh.claim_reimb_id = erb.claim_reimb_id')
//                         ->where('ecr.claim_reimb_id = ecrh.claim_reimb_id')
//                        //->group_by('ecr.claim_reimb_id')
//                        ->get()->result_array();
//print_Pre($data_all);exit;
        ini_set('max_execution_time', 300);
//$z = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//    <soap:Body>
//
//<SubmitClaimRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
//  <ClaimAmount>11</ClaimAmount>
// <ClaimDateOfAdmission>2001-01-01T00:00:00</ClaimDateOfAdmission>
//  <ClaimDateOfDischarge>2001-01-01T00:00:00</ClaimDateOfDischarge>
//  <ClaimSubmissionAttachments>
//    <ClaimSubmissionAttachment>
//      <FileName>siddhes.txt</FileName>
//      <FilePath>https://eb.srabima.com/application/resources/uploads/yogazumba_flexi_benefit/siddhes.txt</FilePath>
//    </ClaimSubmissionAttachment>
//  </ClaimSubmissionAttachments>
//  <ClaimType>reimbursement</ClaimType>
//  <Disease>sdsds</Disease>
//  <EmailId>siddhesh@gmail.com</EmailId>
//  <HospAddress>sdsd</HospAddress>
//  <HospName>sdsd</HospName>
//  <MemberId>5039545400</MemberId>
//  <MobileNo>9232323232</MobileNo>
//  <Password>TM3FA5g</Password>
//  <PolicyNo>431500/48/2020/336</PolicyNo>
//  <ReasonForHospitalization>sdsdsd</ReasonForHospitalization>
//  <UserName>TataMotors</UserName>
//</SubmitClaimRequest>
//
// </soap:Body>
//</soap:Envelope>';
//        echo phpinfo();exit;
        // print_Pre($z);exit;
        $z = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>

<SubmitClaimRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
  <ClaimAmount>' . $data_all["total_claim_amount"] . '</ClaimAmount>
  <ClaimDateOfAdmission>' . $data_all["claim_hospitalization_date"] . '</ClaimDateOfAdmission>
  <ClaimDateOfDischarge>' . $data_all["claim_discharge_date"] . '</ClaimDateOfDischarge>
  <ClaimSubmissionAttachments>
    <ClaimSubmissionAttachment>';
        for ($i = 0; $i < count($data_all_file); $i++) {
            $z .= '<FileName>' . basename($data_all_file[$i]["claim_doc_medical_bill_path"]) . '</FileName>';
            $z .= '<FilePath>' . substr(base_url(), 0, -1) .'application/'. $data_all_file[$i]["claim_doc_medical_bill_path"] . '</FilePath>';
        }
        $z .= '</ClaimSubmissionAttachment>
  </ClaimSubmissionAttachments>
  <ClaimType>reimbursement</ClaimType>
  <Disease>' . $data_all["claim_reimb_disease_name"] . '</Disease>
  <EmailId>' . $data_all["claim_email"] . '</EmailId>
  <HospAddress>' . $data_all["hospital_address"] . '</HospAddress>
  <HospName>' . $data_all["hospital_address"] . '</HospName>
  <MemberId>' . $data["tpa_member_id"] . '</MemberId>
  <MobileNo>' . $data_all["claim_mob"] . '</MobileNo>
  <Password>TM3FA5g</Password>
  <PolicyNo>' . $data["policy_no"] . '</PolicyNo>
  <ReasonForHospitalization>' . $data_all["claim_reimb_reason"] . '</ReasonForHospitalization>
  <UserName>TataMotors</UserName>
</SubmitClaimRequest>

    </soap:Body>
</soap:Envelope>';
        $this->load->library("soaprequest");
        // return $this->soaprequest->getdata($envelope);
        $data = $this->soaprequest->getdata($z, 'https://integration.medibuddy.in/TataMotorsAPI/soap11', array(
            "Content-Type: text/xml",
            "SOAPAction: SubmitClaimRequest",
            "cache-control: no-cache"
        ));

        try {
            $return_data = $data["sBody"]["SubmitClaimResponse"]["ClaimReferenceNo"];
        } catch (Exception $e) {
            $return_data = '';
        }
        if ($return_data) {


            $this->db->where(["claim_reimb_id" => $data['claim_reimb_id']])->update("employee_claim_reimb", [
                "claim_no" => $return_data
            ]);
        }
    }

    public function mediast_claim_details($data) {
        // print_pre($data);exit;
        $z = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>

<ClaimDetailsRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
	  <ClaimId>18770816</ClaimId>
  <ClaimReferenceId>0</ClaimReferenceId>
  <EmployeeCode>0</EmployeeCode>
  <IntimationId>0</IntimationId>
  <MemberId>0</MemberId>
  <Password>TM3FA5g</Password>
  <PolicyNo>431500/48/2020/336</PolicyNo>
  <UserName>TataMotors</UserName>
</ClaimDetailsRequest>

    </soap:Body>
    </soap:Envelope>';


        $this->load->library("soaprequest");
        // return $this->soaprequest->getdata($envelope);
        $data = $this->soaprequest->getdata($z, 'https://integration.medibuddy.in/TataMotorsAPI/soap11', array(
            "Content-Type: text/xml",
            "SOAPAction: ClaimDetailsRequest",
            "cache-control: no-cache"
        ));

        try {
            $response = $data["sBody"]["ClaimDetailsResponse"]["ClaimsData"]["ClaimData"];
        } catch (Exception $ex) {
            return '';
        }
        print_Pre($response);
        exit;
        if ($response["DeductionDetails"]["DeductionDetail"]) {

            $data = $response["DeductionDetails"]["DeductionDetail"];

            for ($i = 0; $i < count($data); $i++) {
                $deduction_amount . $i = $data[$i]["DeductionAmount"];
                $deductiontype . $i = $data[$i]["DeductionType"];
            }

            echo $deduction_amount1;
            echo "<br>";
            echo $deduction_amount1;
            echo "<br>";
            echo $deductiontype1;

            echo "<br>";
            echo $deductiontype2;
            exit;
        }
        $data1 = [
            'claim_reimb_id' => $data["claim_reimb_id"],
            'claim_approve_date' => $response["ClaimApprovalDate"],
            'claim_approve_amount' => $response["ClaimApprovedAmount"],
            'claim_registeration_date' => $response["ClaimRegisterDate"],
            'claim_settlement_date' => $response["ClaimSettlementDate"],
            'status' => $response["ClaimStatus"],
            'claim_type' => $response["ClaimType"],
            'claim_amount' => $response["ClaimedAmount"],
            'date_of_admission' => $response["DateOfAdmission"],
            'dob' => $response["DateOfBirth"],
            'date_of_discharge' => $response["DateOfDischarge"]
        ];

        $this->db->insert('claim_details', $data1);
    }

    //paramount submit claims

    function get_paramount_data_submit_claims($envelope) {
		
		//print_pre($envelope);exit;
//$url = "https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/UPLOAD_MAIN_DOCUMENTS_BROKER_FIRGEN";
//$uat = "https://uat.paramounttpa.com/mwise/MyService.svc/UPLOAD_MAIN_DOCUMENTS_BROKER_FirGen";
        $this->load->library("paramount");
        $data = $this->paramount->getdata($envelope, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/UPLOAD_MAIN_DOCUMENTS_BROKER_FIRGEN', array(
            "Content-Type: application/json",
            "cache-control: no-cache"
        ));
		//print_pre($data);exit;
        $aa = json_decode($data["GeFamilyEnrollmentDetailsResult"], true);
        if (isset($aa["Table"])) {
            return $aa["Table"];
        }
    }

    public function paramount_submit_claims($data) {
        //print_Pre($data);exit;

        $policy_member_data = $this->db->select('*')
                        ->from('employee_policy_member')
                        ->where('policy_member_id', $data['policy_member_id'])
                        ->get()->row_array();
        $dob = (date('Y') - date('Y', strtotime($policy_member_data['policy_mem_dob'])));
        if ($dob <= 0) {
            $dob = 1;
        }


        // print_pre($policy_member_data);exit;

	$data_all = $this->db->select('*')
                        ->from('employee_claim_reimb as ecr,employee_claim_reimb_hospitalization as ecrh')
                        ->where('ecr.claim_reimb_id', $data['claim_reimb_id'])
                        ->where('ecrh.claim_reimb_id = ecr.claim_reimb_id')
                        ->group_by('ecr.claim_reimb_id')
                        ->get()->row_array();
        $data_all_file = $this->db->select('ecd.claim_doc_medical_bill_path')
                        ->from('employee_claim_reimb as ecr,employee_claim_documents as ecd')
                        ->where('ecr.claim_reimb_id', $data['claim_reimb_id'])
                        ->where('ecd.claim_reimb_id = ecr.claim_reimb_id')
                        ->get()->result_array();
						
						// print_pre($data_all_file);exit;

        $start = strtotime($data_all["claim_hospitalization_date"]);
        $end = strtotime($data_all["claim_discharge_date"]);
		

        $length_stay = ceil(abs($end - $start) / 86400); //print_pre($data_all_file);exit;
		//echo $length_stay;exit;
        // print_Pre($data_all_file);exit;
        


//        $data_all_bill_details = $this->db->select('*')
//                        ->from('employee_claim_reimb_hospitalization as ecrh,employee_reimb_bills as erb,employee_claim_reimb as ecr')
//                        ->where('ecrh.claim_reimb_id = erb.claim_reimb_id')
//                         ->where('ecr.claim_reimb_id = ecrh.claim_reimb_id')
//                        //->group_by('ecr.claim_reimb_id')
//                        ->get()->result_array();
//print_Pre($data_all);exit;
        ini_set('max_execution_time', 300);
//$z = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
//    <soap:Body>
//
//<SubmitClaimRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
//  <ClaimAmount>11</ClaimAmount>
// <ClaimDateOfAdmission>2001-01-01T00:00:00</ClaimDateOfAdmission>
//  <ClaimDateOfDischarge>2001-01-01T00:00:00</ClaimDateOfDischarge>
//  <ClaimSubmissionAttachments>
//    <ClaimSubmissionAttachment>
//      <FileName>siddhes.txt</FileName>
//      <FilePath>https://eb.srabima.com/application/resources/uploads/yogazumba_flexi_benefit/siddhes.txt</FilePath>
//    </ClaimSubmissionAttachment>
//  </ClaimSubmissionAttachments>
//  <ClaimType>reimbursement</ClaimType>
//  <Disease>sdsds</Disease>
//  <EmailId>siddhesh@gmail.com</EmailId>
//  <HospAddress>sdsd</HospAddress>
//  <HospName>sdsd</HospName>
//  <MemberId>5039545400</MemberId>
//  <MobileNo>9232323232</MobileNo>
//  <Password>TM3FA5g</Password>
//  <PolicyNo>431500/48/2020/336</PolicyNo>
//  <ReasonForHospitalization>sdsdsd</ReasonForHospitalization>
//  <UserName>TataMotors</UserName>
//</SubmitClaimRequest>
//
// </soap:Body>
//</soap:Envelope>';
//        echo phpinfo();exit;
        // print_Pre($z);exit;
//create unique token
       // $token = time() . rand();
		$token = substr(time() . rand(),0,4);
        $z1 = "false";


        for ($i = 0; $i < count($data_all_file); $i++) {
		
		//print_pre(str_replace("//","/",APPPATH.$data_all_file[$i]["claim_doc_medical_bill_path"]));exit;
            $imagedata = file_get_contents(str_replace("//","/",APPPATH.$data_all_file[$i]["claim_doc_medical_bill_path"]));
			//$imagedata = file_get_contents("/var/www/html/tatabrokers.com/production/html/application/resources/uploads/claim_document/131/docfile0.png");
            $base64 = base64_encode($imagedata);
			// echo '<img src="data:image/jpeg;base64,'.$base64.'" />';
			// $arr = ["base64" => $base64];
			// $arr = json_encode($arr);
			
			// $base64 = json_decode($arr)->base64;
			// print_r($base64);
			 // $base64 = "";
			  // echo '<img src="data:image/jpeg;base64,'.$base64.'" />';
			  // exit;
			
			//echo $base64;exit;
			
			//$base64 = str_replace($base64,"/r/n","");
		
            if ($i == (count($data_all_file) - 1)) {
                $z1 = "true";
                //insert this in  boolis_final
            }
//              $z = [
//            "strphm" => "24634504",
//            "strpatient_name" => "MOHAMMAD ZAMIR",
//            "strTokenNo" =>  $token,
//            "boolis_final" => $z1,
//            "Estimated_Amt" => "11",
//            "admission_dt" => "21/02/2019",
//            "Discharge_dt" => "23/02/2019",
//            "lenght_Stay" => "3",
//            "first_visit_dt" => "21/02/2019",
//            "Patient_Age" => "50",
//            "Provider_No" => "41043",
//            "Ailment" =>  "Testing API",
//            "base64string" => $base64
//            
//            
//        ]; 
            $z = [
                "strphm" => $policy_member_data["tpa_member_id"],
                "strpatient_name" => $policy_member_data["tpa_member_name"],
                "strTokenNo" => $token,
                "boolis_final" => $z1,
                "Estimated_Amt" => $data_all["total_claim_amount"],
                "admission_dt" => date("d/m/Y", strtotime($data_all["claim_hospitalization_date"])),
                "Discharge_dt" => date("d/m/Y", strtotime($data_all["claim_discharge_date"])),
                "lenght_Stay" => $length_stay,
                "first_visit_dt" => date("d/m/Y", strtotime($data_all["claim_hospitalization_date"])),
                "Patient_Age" => $dob,
                "Provider_No" => "0",
                "Ailment" => $data_all["claim_reimb_disease_name"],
                "base64string" => $base64
            ];
            $envelope = json_encode($z);
				//print_pre($envelope);exit;
             $data  = $this->get_paramount_data_submit_claims($envelope);
        }
       // print_pre($envelope);
       // exit;
        try {
            $str = $data["UPLOAD_MAIN_DOCUMENTS_BROKER_FirGenResult"];
            preg_match_all('!\d+!', $str, $matches);
            if (count($matches[0]) == 2) {
                //inward no
                //fir no
                $inward_no = $matches[0][0];
                $fir_no = $matches[0][1];
                $this->db->where(["claim_reimb_id" => $data['claim_reimb_id']])->update("employee_claim_reimb", [
                    "claim_no" => $fir_no
                ]);
            }
        } catch (Exception $e) {
            return '';
            exit;
        }


//            $z = [
//            "strphm" => $policy_member_data["tpa_member_id"],
//            "strpatient_name" => $policy_member_data["tpa_member_name"],
//            "strTokenNo" => generate unique no
//            "boolis_final" => send true at last
//            "Estimated_Amt" => $data_all["total_claim_amount"]
//            "admission_dt" => $data_all["claim_hospitalization_date"]
//            "Discharge_dt" => $data_all["claim_discharge_date"] 
//            "lenght_Stay" => days between the above 2
//            "first_visit_dt" => admission date
//            "Patient_Age" => age 
//            "Provider_No" => will get this from hospital api updated one
//            "Ailment" =>  $data_all["claim_reimb_disease_name"]
//            "base64string" => string file
//            
//            
//        ]; 


        // $z = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    // <soap:Body>

// <SubmitClaimRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
  // <ClaimAmount>' . $data_all["total_claim_amount"] . '</ClaimAmount>
  // <ClaimDateOfAdmission>' . $data_all["claim_hospitalization_date"] . '</ClaimDateOfAdmission>
  // <ClaimDateOfDischarge>' . $data_all["claim_discharge_date"] . '</ClaimDateOfDischarge>
  // <ClaimSubmissionAttachments>
    // <ClaimSubmissionAttachment>';
        // for ($i = 0; $i < count($data_all_file); $i++) {
            // $z .= '<FileName>' . basename($data_all_file[$i]["claim_doc_medical_bill_path"]) . '</FileName>';
            // $z .= '<FilePath>' . substr(base_url(), 0, -1) . $data_all_file[$i]["claim_doc_medical_bill_path"] . '</FilePath>';
        // }
        // $z .= '</ClaimSubmissionAttachment>
  // </ClaimSubmissionAttachments>
  // <ClaimType>reimbursement</ClaimType>
  // <Disease>' . $data_all["claim_reimb_disease_name"] . '</Disease>
  // <EmailId>' . $data_all["claim_email"] . '</EmailId>
  // <HospAddress>' . $data_all["hospital_address"] . '</HospAddress>
  // <HospName>' . $data_all["hospital_address"] . '</HospName>
  // <MemberId>' . $data["tpa_member_id"] . '</MemberId>
  // <MobileNo>' . $data_all["claim_mob"] . '</MobileNo>
  // <Password>TM3FA5g</Password>
  // <PolicyNo>' . $data["policy_no"] . '</PolicyNo>
  // <ReasonForHospitalization>' . $data_all["claim_reimb_reason"] . '</ReasonForHospitalization>
  // <UserName>TataMotors</UserName>
// </SubmitClaimRequest>

    // </soap:Body>
// </soap:Envelope>';
       // $this->load->library("soaprequest");
        // return $this->soaprequest->getdata($envelope);
        // $data = $this->soaprequest->getdata($z, 'https://integration.medibuddy.in/TataMotorsAPI/soap11', array(
            // "Content-Type: text/xml",
            // "SOAPAction: SubmitClaimRequest",
            // "cache-control: no-cache"
        // ));

        // try {
            // $return_data = $data["sBody"]["SubmitClaimResponse"]["ClaimReferenceNo"];
        // } catch (Exception $e) {
            // $return_data = '';
        // }
        // if ($return_data) {


            // $this->db->where(["claim_reimb_id" => $data['claim_reimb_id']])->update("employee_claim_reimb", [
                // "claim_no" => $return_data
            // ]);
        // }
    }

function paramount_claim_details($data1) {
        //echo "here";exit;
        //echo "sdsdsdsdsd";exit;
      // $data1["policy_no"] = "112300/48/2020/347";

        $z = [
            "USERNAME" => "TATA-MOTORS",
            "PASSWORD" => "ADMIN@123",
            "POLICY_NO" => $data1["policy_no"]
        ];

        $z = json_encode($z);
		//echo $z;exit;
        $this->load->library("paramount");
        $data = $this->paramount->getdata($z, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/GET_IPD_CLAIM_DETAILS', array(
            "Content-Type: application/json",
            "cache-control: no-cache"
        ));
        
        try {
            $result = json_decode($data["GET_IPD_CLAIM_DETAILSResult"], true);
           // print_pre($result);exit;
            //$result = $aa[0];
        } catch (Exception $e) {
            return '';
        }
          for ($i = 0; $i < count($result); $i++) {
            $z = [
                "tpa_member_id" => $result[$i]["MEMBERUHID"],
                "claim_details" => json_encode($result[$i]),
                "policy_no" => $data1["policy_no"],
                "tpa_id" => 2
            ];
            $this->db->insert('tpa_claim_details', $z);
            
        }
        echo "sdsd";exit;
    }


    // function paramount_claim_details($data) {

        // $z = [
            // "USERNAME" => "TATA-MOTORS",
            // "PASSWORD" => "ADMIN@123",
            // "FIR" => $data["claim_no"]
        // ];
	
        // $z = json_encode($z);
		// print_pre($z);exit;
        // $this->load->library("paramount");
        // $data = $this->paramount->getdata($z, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/GET_IPD_CLAIM_DETAILS', array(
            // "Content-Type: application/json",
            // "cache-control: no-cache"
        // ));

        // try {
            // $result = json_decode($data["GET_IPD_CLAIM_DETAILSResult"], true);
         //  $result = $aa[0];
        // } catch (Exception $e) {
            // return '';
        // }
        // for ($i = 0; $i < count($result); $i++) {
            // $z = [
                // "tpa_member_id" => $result[$i]["MEMBERUHID"],
                // "claim_details" => json_encode($result[$i]),
				// "policy_no" =>
            // ];
            // $this->db->insert('tpa_claim_details', $z);
        // }
    // }

    function health_india_enrollment($data) {
	//echo "sdsdsd";exit;
        $array = [
            "UserName" => "Rdo7L2MaIwmMXKSiKrefKg==",
            "Password" => "x51N2RZvkTsOYBZxASfwijCUe48DvM6bVHQx3gbzPtU=",
            "EmployeeCode" => $data["emp_code"],
            "PolicyNo" => $data["policy_no"]
        ];
		
		//
		$subQuery1 = $this->db
                    ->select('"self" as "fr_name", epd.policy_no,epm.policy_member_id,ed.bdate')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_details as ed')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id', 0)
                    ->where('fr.emp_id', $data["emp_id"])
                 //   ->where('ed.fr_id = mfr.fr_id')
                    ->where('fr.emp_id = ed.emp_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->group_by('epm.policy_member_id')
                    ->get_compiled_select();
            $subQuery2 = $this->db
                    ->select('mfr.fr_name, epd.policy_no,epm.policy_member_id,efd.family_dob')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_family_details as efd, master_family_relation as mfr')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id = efd.family_id')
                    ->where('efd.fr_id=mfr.fr_id')
                    ->where('fr.emp_id', $data['emp_id'])
                    ->where('efd.fr_id = mfr.fr_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->get_compiled_select();

            $op = $this->db->query($subQuery1 . ' UNION ' . $subQuery2)->result_array();
			print_pre($this->db->last_query());exit;
		
		//



        $z = json_encode($array);
        $this->load->library("paramount");
        $data1 = $this->paramount->getdata($z, 'https://software.healthindiatpa.com/HiWebApi/Tata/EnrollmentDataRequest', array(
            "Content-Type: application/json",
            "cache-control: no-cache", "health_india:true"
        ));
	$search = 'Message';
if(preg_match("/{$search}/i", $data1)) {
    return;
}
        $xml = simplexml_load_string($data1);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
		//print_pre($array);exit;
        try {

            $result = $array["TATAMappingEnrollment"];
            if (!isset($result[0])) {
                //echo "here";exit;
                $result[0] = $result;
            }

            $subQuery1 = $this->db
                    ->select('"self" as "fr_name", epd.policy_no,epm.policy_member_id,ed.bdate')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_details as ed')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id', 0)
                    ->where('fr.emp_id', $data["emp_id"])
                 //   ->where('ed.fr_id = mfr.fr_id')
                    ->where('fr.emp_id = ed.emp_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->group_by('epm.policy_member_id')
                    ->get_compiled_select();
            $subQuery2 = $this->db
                    ->select('mfr.fr_name, epd.policy_no,epm.policy_member_id,efd.family_dob')
                    ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_family_details as efd, master_family_relation as mfr')
                    ->where('epd.policy_detail_id = epm.policy_detail_id')
                    ->where('epm.family_relation_id = fr.family_relation_id')
                    ->where('fr.family_id = efd.family_id')
                    ->where('efd.fr_id=mfr.fr_id')
                    ->where('fr.emp_id', $data['emp_id'])
                    ->where('efd.fr_id = mfr.fr_id')
                    ->where('epd.policy_no', $data["policy_no"])
                    ->get_compiled_select();

            $op = $this->db->query($subQuery1 . ' UNION ' . $subQuery2)->result_array();
			print_pre($this->db->last_query());exit;
            for ($j = 0; $j < count($result); $j++) {

                for ($k = 0; $k < count($op); $k++) {

                    if (strtolower($result[$j]["relationship"]) == "employee") {
                        $result[$j]["relationship"] = "SELF";
                    }
                    if (strtolower($result[$j]["relationship"]) == "wife" || strtolower($result[$j]["relationship"]) == "husband") {
                        $result[$j]["relationship"] = "Spouse/Partner";
                    }
                    //print_Pre(strtolower($result[$j]["relationship"])." ==". strtolower($op[$k]["fr_name"]));
                    //echo "<br>";
                    // print_Pre(date("d-m-Y", strtotime($result[$j]["DOB"]))." ==".$op[$k]["bdate"]);

                    if (strtolower($result[$j]["relationship"]) == strtolower($op[$k]["fr_name"]) && (date("d-m-Y", strtotime($result[$j]["DOB"])) == $op[$k]["bdate"])) {
						
                        $this->db->where(["policy_member_id" => $op[$k]["policy_member_id"]])->update("employee_policy_member", ["tpa_member_id" => $result[$j]["MemberUHIDNo"], "tpa_member_name" => $result[$j]["MemberName"]]);
                        //insert into new database
						//print_Pre($result[$j]);exit;
                        $insert_data = [
                            "MemberUHIDNo" => $result[$j]["MemberUHIDNo"],
                            "enrollment_details" => json_encode($result[$j])
                        ];
                        $this->db->insert('tpa_enrollment_data', $insert_data);
						
						
                    }
                }
            }
        } catch (Exception $e) {
            return '';
        }
    }

    function health_india_network_hospitals($data) {

//        $array = [
//            "UserName" => "TATA-MOTORS",
//            "Password" => "ADMIN@123",
//            "PolicyNo" => $data["policy_no"]
//        ];
        $array = [
            "UserName" => "Rdo7L2MaIwmMXKSiKrefKg==",
            "Password" => "x51N2RZvkTsOYBZxASfwijCUe48DvM6bVHQx3gbzPtU=",
            "PolicyNo" => $data["policy_no"]
        ];

        $envelope = json_encode($array);
        $this->load->library("paramount");
        $data1 = $this->paramount->getdata($envelope, 'https://software.healthindiatpa.com/HiWebApi/Tata/GetNetworkHospitals', array(
            "Content-Type: application/json",
            "cache-control: no-cache", "health_india:true"
        ));
        $data1 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $data1);
        $xml = simplexml_load_string($data1);
        $json = json_encode($xml);
        $result = json_decode($json, TRUE);
        $result = $result["UniversalMappingNetwork"];

        for ($i = 0; $i < count($result); $i++) {
            $network[$i] = [
                "policy_no" => $data["policy_no"],
                "HOSIDNO1" => $result[$i]["HospitalId"],
                "ADDRESS1" => (empty($result[$i]["AddressLine1"]) ? "" : $result[$i]["AddressLine1"]) . " " . (empty($result[$i]["AddressLine2"]) ? "" : $result[$i]["AddressLine2"]),
                "HOSPITAL_NAME" => (empty($result[$i]["HospitalName"]) ? "" : $result[$i]["HospitalName"]),
                "PIN_CODE" => (empty($result[$i]["Pincode"]) ? "" : $result[$i]["Pincode"]),
                "PHONE_NO" => (empty($result[$i]["PhoneNumber"]) ? "" : $result[$i]["PhoneNumber"]),
                "CITY_NAME" => (empty($result[$i]["CityName"]) ? "" : $result[$i]["CityName"]),
                "STATE_NAME" => (empty($result[$i]["StateName"]) ? "" : $result[$i]["StateName"]),
                "TPA_ID" => "4"
            ];
        }
        $this->db->trans_start();
        $this->db->insert_batch('network_hospitals', $network);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            return FALSE;
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
			//echo "check";exit;
            return TRUE;
        }
    }
function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}
    function health_india_submit_claims($data) {



        $policy_member_data = $this->db->select('*')
                        ->from('employee_policy_member,employee_policy_detail')
                        ->where('policy_member_id', $data['policy_member_id'])
                        ->where('employee_policy_member.policy_detail_id = employee_policy_detail.policy_detail_id')
                        ->get()->row_array();
        // print_pre($policy_member_data);exit;

        $data_all_file = $this->db->select('ecd.claim_doc_medical_bill_path')
                        ->from('employee_claim_reimb as ecr,employee_claim_documents as ecd')
                        ->where('ecr.claim_reimb_id', $data['claim_reimb_id'])
                        ->where('ecd.claim_reimb_id = ecr.claim_reimb_id')
                        ->get()->result_array();
        $data_all = $this->db->select('*')
                        ->from('employee_claim_reimb as ecr,employee_claim_reimb_hospitalization as ecrh')
                        ->where('ecr.claim_reimb_id', $data['claim_reimb_id'])
                        ->where('ecrh.claim_reimb_id = ecr.claim_reimb_id')
                        ->group_by('ecr.claim_reimb_id')
                        ->get()->row_array();
        // print_pre($data_all_file);exit;
        $file_array = [];
        $count = 0;
        for ($i = 0; $i < count($data_all_file); $i++) {
            $count++;
			//print_pre($data_all_file);
			$data_all_file[$i]["claim_doc_medical_bill_path"] = $this->str_replace_first("/", "", $data_all_file[$i]["claim_doc_medical_bill_path"]);
			//echo APPPATH . $data_all_file[$i]["claim_doc_medical_bill_path"];exit;
            $imagedata = file_get_contents(APPPATH . $data_all_file[$i]["claim_doc_medical_bill_path"]);
            $base64 = base64_encode($imagedata);
            $file_array[] = $base64;
        }
        try {
            $request_array = [
                "MemberId" => $policy_member_data["tpa_member_id"], //$policy_member_data["tpa_member_id"]
                "PolicyNumber" => $policy_member_data["policy_no"], //policy_no
                "PatientName" => $policy_member_data["tpa_member_name"], //tpa_member_name
                "Uploadpath" => "",
                "FileName" => "",
                "PdfBytes" => $file_array,
                "NumberofFile" => $count,
                "DocumentType" => "Reimbursement",
                "UserName" => "Rdo7L2MaIwmMXKSiKrefKg==",
                "Password" => "x51N2RZvkTsOYBZxASfwijCUe48DvM6bVHQx3gbzPtU="
            ];

            $envelope = json_encode($request_array);
			//print_pre($envelope);exit;
            $this->load->library("paramount");
            $data1 = $this->paramount->getdata($envelope, 'https://software.healthindiatpa.com/HiWebApi/Tata/ClaimSubmissionRequest', array(
                "Content-Type: application/json",
                "cache-control: no-cache", "health_india:true"
            ));
            $data1 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $data1);
            $xml = simplexml_load_string($data1);
            $json = json_encode($xml);
            $result = json_decode($json, TRUE);
			//PRINT_PRE($result);EXIT;
            $claim_no = $result["TataMappingClaimSubmission"]["InwardNumber"];

            $this->db->set('claim_no', $claim_no); //value that used to update column  
            $this->db->where('claim_reimb_id', $data['claim_reimb_id']); //which row want to upgrade  
            $this->db->update('employee_claim_reimb');
        }

//catch exception
        catch (Exception $e) {
            
        }
    }

    function health_india_claim_details($data) {

        $request_array = [
            "ClaimId" => "",
            "ClaimReferenceId" => "",
            "EmployeeCode" => "",
            "IntimationId" => "",
            "MemberId" => "",
            "PolicyNo" => $data["policy_no"], //$data["policy_no"]
            "UserName" => "Rdo7L2MaIwmMXKSiKrefKg==",
            "Password" => "x51N2RZvkTsOYBZxASfwijCUe48DvM6bVHQx3gbzPtU="
        ];
        // print_pre(json_encode($request_array));exit;

        $envelope = json_encode($request_array);
        $this->load->library("paramount");
        $data1 = $this->paramount->getdata($envelope, 'https://software.healthindiatpa.com/HiWebApi/Tata/ClaimDetailsRequest', array(
            "Content-Type: application/json",
            "cache-control: no-cache", "health_india:true"
        ));
        $data1 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $data1);
        $xml = simplexml_load_string($data1);
        $json = json_encode($xml);
        $result = json_decode($json, TRUE);
        $result = $result["TATAMappingClaims"];

        for ($i = 0; $i < count($result); $i++) {
            $z = [
                "tpa_member_id" => $result[$i]["UHIDNO"],
                "claim_details" => json_encode($result[$i])
            ];
            $this->db->insert('tpa_claim_details', $z);
        }
        //  echo "done";exit;
    }

    function count_array_values($my_array, $match) {
        $count = 0;

        foreach ($my_array as $key => $value) {
            if ($value == $match) {
                $count++;
            }
        }

        return $count;
    }

    function give_mfr_from_table($relationship) {
        $data = $this->db->select("fr_id")->where("LOWER(fr_name)", $relationship)->get(master_family_relation)->row_array();
        //  echo $this->db->last_query();exit;
        return ($data["fr_id"]);
    }

    function fhpl_enrollment($data) {
        //  echo encrypt_decrypt_password("TW1jYUhUVVNnVWdYd1IwM2RwTktmdz09","D");EXIT;
        // print_pre($data);exit;
        // echo "here";exit;
        //<tem:PolicyNumber>'+$data["policy_no"]+'</tem:PolicyNumber>  

        $envelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://tempuri.org/IBCService/GetenrollmentDetails_TATA</wsa:Action></soap:Header>
<soap:Body>
<tem:GetenrollmentDetails_TATA>

<tem:UserName>TataMotors</tem:UserName>

<tem:Password>fhgn179ta</tem:Password>

<tem:GroupCode>16137</tem:GroupCode>

<tem:PolicyNumber>'.$data["policy_no"].'</tem:PolicyNumber>
</tem:GetenrollmentDetails_TATA>
</soap:Body>
</soap:Envelope>';


        try {
            $this->load->library("soaprequest");
            // return $this->soaprequest->getdata($envelope);
            $data1 = $this->soaprequest->getdata($envelope, 'https://m.fhpl.net/Bunnyconnect/BCService.svc', array(
                "content-type: application/soap+xml;charset=UTF-8;",
                "fhpl: true"
            ));
            $envelope = json_decode($data1['sBody']['GetenrollmentDetails_TATAResponse']['GetenrollmentDetails_TATAResult'], true);
			//print_pre($envelope);exit;
			
        }

//catch exception
        catch (Exception $e) {
            
        }
        for ($i = 0; $i < count($envelope); $i++) {
            if (strtolower($envelope[$i]["relationship"]) == "self") {
                $subQuery1 = $this->db
                        ->select('"self" as "fr_name", epd.policy_no,epm.policy_member_id,ed.bdate')
                        ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_details as ed')
                        ->where('epd.policy_detail_id = epm.policy_detail_id')
                        ->where('epm.family_relation_id = fr.family_relation_id')
                        ->where('fr.family_id', 0)
                        //->where('fr.emp_id', $data["emp_id"])
                        // ->where('ed.fr_id = mfr.fr_id')
                        ->where('fr.emp_id = ed.emp_id')
                        ->where('epd.policy_no', $data["policy_no"])
                        ->where('ed.emp_code', $envelope[$i]["EmployeeID"])
                        ->group_by('epm.policy_member_id')
                        ->get()
                        ->row_array();
                //print_pre($this->db->last_query());exit;
				//echo "subquery=".$subQuery1['bdate']."envelope=".date("d-m-Y", strtotime($envelope[$i]["DOB"]));echo "<br>";exit;
                if ($subQuery1["bdate"] == date("d-m-Y", strtotime($envelope[$i]["DOB"]))) {
                    // echo "here";
                    //print_pre($subQuery2);exit;
                    $this->db->where(["policy_member_id" => $subQuery1["policy_member_id"]])->update("employee_policy_member", ["tpa_member_id" => $envelope[$i]["MemberUHIDNo"], "tpa_member_name" => $envelope[$i]["EmployeeID"]]);
					//echo $this->db->last_query();exit;
                    //print_pre($envelope[$i]);exit;
					$insert_data = [
                            "MemberUHIDNo" => $envelope[$i]["MemberUHIDNo"],
                            "enrollment_details" => json_encode($envelope[$i])
                        ];
                        $this->db->insert('tpa_enrollment_data', $insert_data);
					
                }
            } else {
				


                if (strtolower($envelope[$i]["relationship"]) == "wife" || strtolower($envelope[$i]["relationship"]) == "husband") {
                    $envelope[$i]["relationship"] = "spouse/partner";
                }
                $get_mfr_id = $this->give_mfr_from_table(strtolower($envelope[$i]["relationship"]));
               // $data["policy_no"] = 'sid123';
                //$envelope[$i]["EmployeeID"] = '105';


                $subQuery2 = $this->db
                        ->select('mfr.fr_name, epd.policy_no,epm.policy_member_id,efd.family_dob')
                        ->from('employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_family_details as efd, master_family_relation as mfr,employee_details as ed')
                        ->where('epd.policy_detail_id = epm.policy_detail_id')
                        ->where('epm.family_relation_id = fr.family_relation_id')
                        ->where('fr.family_id = efd.family_id')
                        ->where('efd.fr_id=mfr.fr_id')
                        ->where('fr.emp_id = ed.emp_id')
                        ->where('efd.fr_id = mfr.fr_id')
                        ->where('epd.policy_no', $data["policy_no"])
                        ->where('ed.emp_code', $envelope[$i]["EmployeeID"])
                        ->where('mfr.fr_id', $get_mfr_id)
                        ->get()
                        ->row_array();
						
						


               // $subQuery2["family_dob"] = '05-03-1969';
                 //echo "subquery-dob-".$subQuery2["family_dob"]."==".date("d-m-Y", strtotime($envelope[$i]["DOB"]));echo "<br>";exit;

                if ($subQuery2["family_dob"] == date("d-m-Y", strtotime($envelope[$i]["DOB"]))) {
                    // echo "here";
                    //print_pre($subQuery2);exit;
                    $this->db->where(["policy_member_id" => $subQuery2["policy_member_id"]])->update("employee_policy_member", ["tpa_member_id" => $envelope[$i]["MemberUHIDNo"], "tpa_member_name" => $envelope[$i]["EmployeeID"]]);
                    //print_pre($envelope[$i]);exit;
					//echo $this->db->last_query();exit;
					
					
					 $insert_data = [
                            "MemberUHIDNo" => $envelope[$i]["MemberUHIDNo"],
                            "enrollment_details" => json_encode($envelope[$i])
                        ];
                        $this->db->insert('tpa_enrollment_data', $insert_data);
                }
                // echo "sdfsd";exit;
            }
        }
    }

    function fhpl_network_hospitals($data) {
		//print_pre($data);exit;
        $envelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://tempuri.org/IBCService/GetNetworkHospitalDetails</wsa:Action></soap:Header>
<soap:Body>
<tem:GetNetworkHospitalDetails>
<tem:UserName>TataMotors</tem:UserName>
<tem:Password>fhgn179ta</tem:Password>
<tem:StartIndex>1</tem:StartIndex>
<tem:EndIndex>5000</tem:EndIndex>
     </tem:GetNetworkHospitalDetails>
  </soap:Body>
</soap:Envelope>';

        try {
            $this->load->library("soaprequest");
            // return $this->soaprequest->getdata($envelope);
            $data1 = $this->soaprequest->getdata($envelope, 'https://m.fhpl.net/Bunnyconnect/BCService.svc', array(
                "content-type: application/soap+xml;charset=UTF-8;",
                "fhpl: true"
            ));
            
             $envelope = json_decode($data1['sBody']['GetNetworkHospitalDetailsResponse']['GetNetworkHospitalDetailsResult'], true);
            $network_hospitals = $envelope["Table1"];
			
            
        }

//catch exception
        catch (Exception $e) {
            
        }

        //print_pre($result);exit;


        for ($i = 0; $i < count($network_hospitals); $i++) {
            $data2[] = [
                'HOSIDNO1' => ($network_hospitals[$i]['HospitalId']) ? $network_hospitals[$i]['HospitalId'] : '',
                'policy_no' => ($data['policy_no']) ? $data['policy_no'] : '',
               // 'PARTNER_ID' => ($network_hospitals[$i]['PARTNER_ID']) ? $network_hospitals[$i]['PARTNER_ID'] : '',
               // 'ZONE_NAME' => ($network_hospitals[$i]['ZONE_NAME']) ? $network_hospitals[$i]['ZONE_NAME'] : '',
                'HOSPITAL_NAME' => ($network_hospitals[$i]['HospitalName']) ? $network_hospitals[$i]['HospitalName'] : '',
                'ADDRESS1' => ($network_hospitals[$i]['AddressLine1']) ? $network_hospitals[$i]['AddressLine1'] : '',
                'ADDRESS2' => ($network_hospitals[$i]['AddressLine2']) ? $network_hospitals[$i]['AddressLine2'] : '',
                'CITY_NAME' => ($network_hospitals[$i]['CityName']) ? $network_hospitals[$i]['CityName'] : '',
                'STATE_NAME' => ($network_hospitals[$i]['stateName']) ? $network_hospitals[$i]['stateName'] : '',
                'PIN_CODE' => ($network_hospitals[$i]['Pincode']) ? $network_hospitals[$i]['Pincode'] : '',
                'LANDMARK_1' => ($network_hospitals[$i]['Landmark1']) ? $network_hospitals[$i]['Landmark1'] : '',
                'LANDMARK_2' => ($network_hospitals[$i]['Landmark2']) ? $network_hospitals[$i]['Landmark2'] : '',
                'PHONE_NO' => ($network_hospitals[$i]['PhoneNumber']) ? $network_hospitals[$i]['PhoneNumber'] : '',
                'EMAIL' => ($network_hospitals[$i]['Email']) ? $network_hospitals[$i]['Email'] : '',
                'LEVEL_OF_CARE' => ($network_hospitals[$i]['LevelOfCare']) ? $network_hospitals[$i]['LevelOfCare'] : '',
                'ISHOSPITALACTIVE' => ($network_hospitals[$i]['ISHOSPITALACTIVE']) ? $network_hospitals[$i]['ISHOSPITALACTIVE'] : '',
                'Insurance_Company' => ($network_hospitals[$i]['Insurance_Company']) ? $network_hospitals[$i]['Insurance_Company'] : '',
                'HOSP_CREATED_ON' => ($network_hospitals[$i]['HOSP_CREATED_ON']) ? $network_hospitals[$i]['HOSP_CREATED_ON'] : '',
                'HOSP_MODIFIED_ON ' => ($network_hospitals[$i]['HOSP_MODIFIED_ON']) ? $network_hospitals[$i]['HOSP_MODIFIED_ON'] : '',
                "TPA_ID" => "5"
            ];
			//print_pre($data2);exit;
        }
      
        
        
         $this->db->trans_start();
        $this->db->insert_batch('network_hospitals', $data2);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
           // echo "here";exit;
            $this->db->trans_rollback();
            return FALSE;
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
           // echo "check";exit;
            return TRUE;
        }
    }
    function fhpl_claim_details($data){
		
		//$data["policy_no"] = '71250034180400000035';
         $envelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
   <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://tempuri.org/IBCService/GetClaimsdetails_TATA</wsa:Action></soap:Header>
   <soap:Body>
      <tem:GetClaimsdetails_TATA>
	  <tem:UserName>TataMotors</tem:UserName>
		 <tem:Password>fhgn179ta</tem:Password>
		 <tem:GroupCode>16137</tem:GroupCode>
		 <tem:PolicyNumber>'.$data["policy_no"].'</tem:PolicyNumber>
      </tem:GetClaimsdetails_TATA>
   </soap:Body>
</soap:Envelope>';
                  try {
            $this->load->library("soaprequest");
            // return $this->soaprequest->getdata($envelope);
            $data1 = $this->soaprequest->getdata($envelope, 'https://m.fhpl.net/Bunnyconnect/BCService.svc', array(
                "content-type: application/soap+xml;charset=UTF-8;",
                "fhpl: true"
            ));
           // print_pre($data1);exit;
             $result = json_decode($data1['sBody']['GetClaimsdetails_TATAResponse']['GetClaimsdetails_TATAResult'], true);
			 //print_Pre($result);exit;
              for ($i = 0; $i < count($result); $i++) {
            $z = [
                "tpa_member_id" => $result[$i]["uhidno"],
                "claim_details" => json_encode($result[$i])
            ];
			//print_pre($z);exit;
            $this->db->insert('tpa_claim_details', $z);
			echo "check";exit;
        }
//        echo "check";exit;
            
           
        }

//catch exception
        catch (Exception $e) {
            
        }
        
    }
	
		function sendTransactionalSms($template, $hash_values, $other_data)
    {
        $message = $this->getSmsMessage($template, $hash_values);
		$senderID = 1;
		
			$AlertV1 = '';
			$AlertV2 = '';
			$AlertV3 = '';
			$AlertV4 = '';
			$AlertV5 = '';
			
			if($other_data['template_id'] == 'A1133'){
				$AlertV1 = $other_data['premium'];
				$AlertV3 = $other_data['product_name'];
				$AlertV4 = $other_data['premium'];
				$AlertV5 = $other_data['url'];
			}elseif($other_data['template_id'] == 'A1131'){
				$AlertV2 = $other_data['url'];
			}elseif($other_data['template_id'] == 'A1132'){
				$AlertV1 = $other_data['product_name'];
				$AlertV2 = $other_data['url'];
			}elseif($other_data['template_id'] == 'A1136'){
				$AlertV1 = $other_data['product_name'];
				$AlertV2 = $other_data['url'];
			}elseif($other_data['template_id'] == 'A1130'){
				$AlertV1 = $other_data['url'];
			}
			
		
        $parameters =[
		"RTdetails" => [
       
            "PolicyID" => '',
            "AppNo" => 'HD100017934',
            "alertID" => $other_data['template_id'],
            "channel_ID" => $other_data['product_name'],
            "Req_Id" => 1,
            "field1" => '',
            "field2" => '',
            "field3" => '',
            "Alert_Mode" => 3,
            "Alertdata" => 
                [
                    "mobileno" => $other_data['mob_no'],
                    "emailId" => $other_data['email'],
                    "AlertV1" => $AlertV1,
                    "AlertV2" => $AlertV2,
                    "AlertV3" => $AlertV3,
                    "AlertV4" => $AlertV4,
                    "AlertV5" => $AlertV5,
                    "AlertV6" => '',
                    "AlertV7" => '',
                    "AlertV8" => '',
                    "AlertV9" => '',
                    "AlertV10" => '',
                    "AlertV11" => '',
                    "AlertV12" => '',
                    "AlertV13" => '',
                    "AlertV14" => '',
                    "AlertV15" => '',
                    "AlertV16" => '',
                    "AlertV17" => '',
                    "AlertV18" => '',
                    "AlertV19" => '',
                    "AlertV20" => '',
                    "AlertV21" => '',
                    "AlertV22" => '',
                    "AlertV23" => '',
                    "AlertV24" => '',
                    "AlertV25" =>'',
                    "AlertV26" =>'',
                    "AlertV27" =>'',
                    "AlertV28" =>'',
                    "AlertV29" =>'',
                    "AlertV30" =>'',
                    "AlertV31" =>'',
                    "AlertV32" =>'',
                    "AlertV33" =>'',
                    "AlertV34" =>'',
                    "AlertV35" =>'' ,
                    "AlertV36" => '',
                    "AlertV37" => '',
                    "AlertV38" => '',
                    "AlertV39" => '',
                    "AlertV40" => '',
                    "AlertV41" => '',
                    "AlertV42" => '',
                    "AlertV43" => '',
                    "AlertV44" => '',
                    "AlertV45" =>'' ,
                    "AlertV46" =>'' ,
                    "AlertV47" =>'' ,
                    "AlertV48" =>'' ,
                    "AlertV49" =>'' ,
                    "AlertV50" =>'' ,
                ]

			]

		];
		 $parameters = json_encode($parameters);
		 $curl = curl_init();
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://10.1.226.32/ABHICL_ClickPSS/Service1.svc/click",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $parameters,
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
		   
		  ),
		));

	$response = curl_exec($curl);
	
	$request_arr = ["lead_id" => $hash_values['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) , "type"=>"sms_logs"];
	$this->db->insert("logs_docs",$request_arr);
	
	// $request_arr1 = ["lead_id" => $hash_values['lead_id'], "req" => json_encode($message), "type"=>"msg_template"];
	// $this->db->insert("logs_docs",$request_arr1);
		  
	return $response;
       
   
    }
	
	function getSmsMessage($message_text, $hashvalues)
    {
        preg_match_all('/((#\w+\b#))/i', $message_text, $matches);
        for ($i = 0; $i < count($matches[1]); $i++) {
            $key = $matches[1][$i];
            $value = $matches[2][$i];
            $$key = $value;
            $message_text = str_replace($key, $hashvalues[$i], $message_text);
        }

        return $message_text;
    }
	
	/*
	* This Function is Use to Add Members Of Specific Lead Imto AXIS CRM
	* Created By Shardul Kulkarni<shardul.kulkarni@fyntune.com> 
	* Created Date : 27-Aug-2020
	*/
	public function insertMemberCRMDropOff($emp_id,$crmLeadId){
		// Create LEAD CRM Start
		
		$this->db1 = $this->load->database('axis_retail',TRUE);
		
		// Get Employee Details
		$sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
		$empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
		$customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",$empDetailsArray['customer_name']) : "";
		
		// Get DOB
		// $dateOfBirth = date("Y-m-d",strtotime($dob));
		$empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
		
		// DOB to Age Convertation Part Start
		$empAge = 0;
		if(!empty($empDOB)) {
			$today = date("Y-m-d");
			$diff = date_diff(date_create($empDOB), date_create($today));
			$empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;			
		}
		// DOB to Age Convertation Part End
		
		// Get Policy Details Start
		$queryGetProposalDetails = $this->db1->query("SELECT * FROM proposal WHERE emp_id = '".$emp_id."'")->row_array();
		$proposalNumber = !empty($queryGetProposalDetails['proposal_no']) ? $queryGetProposalDetails['proposal_no'] : "";
		$productId 		= !empty($queryGetProposalDetails['product_id']) ? $queryGetProposalDetails['product_id'] : "";
		$sumInsured 	= !empty($queryGetProposalDetails['sum_insured']) ? $queryGetProposalDetails['sum_insured'] : "";
		$premiumAmount 		= !empty($queryGetProposalDetails['premium']) ? $queryGetProposalDetails['premium'] : "";
		// Get Policy Details End
		
		// Get Product Details Start
		$productName = "";
			
		if(!empty($productId)) {
			$queryGetProposalDetails = $this->db1->query("SELECT * FROM proposal WHERE emp_id = '".$emp_id."'")->row_array();
			$proposalNumber = !empty($queryGetProposalDetails['proposal_no']) ? $queryGetProposalDetails['proposal_no'] : "";
			
			$prodIdMain = !empty($queryGetProposalDetails['product_id']) ? $queryGetProposalDetails['product_id'] : "";
			$productData = $this->db1->query("SELECT * FROM product_master_with_subtype WHERE policy_parent_id = '".$prodIdMain."'")->row_array();
			$productName = !empty($productData['product_name']) ? $productData['product_name'] : "";
			$masterPolicy = !empty($productData['master_policy_no']) ? $productData['master_policy_no'] : "";
		} else {
			$productData = $this->db1->query("SELECT * FROM product_master_with_subtype")->row_array();
			$productName = !empty($productData['product_name']) ? $productData['product_name'] : "";
			$masterPolicy = !empty($productData['master_policy_no']) ? $productData['master_policy_no'] : "";
		}
		
		// Get Product Details End
		
		$memberInsertationArray = array();
		$optionalCoverArray		= array();
		$bundleProduct			= array();
		$bundleMember			= array();
		
		// Get And Create Policy Member Data Array Start
		$queryGetProposalMemberDetailsObj = $this->db1->query("SELECT EPM.policy_member_id, EPM.policy_member_first_name, EPM.age, EPM.policy_mem_sum_insured, EPM.policy_mem_sum_premium, EPM.family_type_name, FR.family_id FROM employee_policy_member AS EPM, family_relation AS FR WHERE EPM.family_relation_id = FR.family_relation_id AND FR.emp_id = '".$emp_id."'")->result();

		$sumInsured = "";
		$premiumAmount = "";
		$familyTypeName = "";
		foreach($queryGetProposalMemberDetailsObj as $key=>$mainMember) {
			
			$memberRelation = $this->db1->query("SELECT fr_name FROM master_family_relation WHERE fr_id = '".$mainMember->family_id."'")->row_array();
			
			if(empty($memberRelation['fr_name'])) {
				$memberFRID = $this->db1->query("SELECT fr_id FROM employee_family_details WHERE family_id = '".$mainMember->family_id."'")->row_array();
				
				if(!empty($memberFRID['fr_id'])) {
					$memberRelation = $this->db1->query("SELECT fr_name FROM master_family_relation WHERE fr_id = '".$memberFRID['fr_id']."'")->row_array();
				}
			}
			
			$sumInsured = !empty($mainMember->policy_mem_sum_insured) ? $mainMember->policy_mem_sum_insured : "";
			$premiumAmount = !empty($mainMember->policy_mem_sum_premium) ? $mainMember->policy_mem_sum_premium : "";
			
			if($mainMember->family_id == 0) {
				$familyTypeName = str_replace("+",", ",$mainMember->family_type_name);
			}
			
			$policyMemberData[] = array( "BundleMemberId"=> !empty($mainMember->policy_member_id) ? $mainMember->policy_member_id : "",
									"BundleMemberName"=> !empty($mainMember->policy_member_first_name) ? $mainMember->policy_member_first_name : "",
									"BundleMemberAge"=> !empty($mainMember->age) ? $mainMember->age : "",
									"BundleMemberProduct"=> $productName,
									"BundleMemberPlan"=> "",
									"BundleMemberPremium"=> $premiumAmount,
									"BundleMemberSumInsured"=> $sumInsured,
									"BundleMemberRelationship"=> !empty($memberRelation['fr_name']) ? $memberRelation['fr_name'] : ""
								 );
		}
		
		
		$bundleProduct['BundleFlag']= "";
		$bundleProduct['BundleFamilyConstruct']= $familyTypeName;
		$bundleProduct['BundleProduct']= $productName;
		$bundleProduct['BundlePremium']= $premiumAmount;
		$bundleProduct['BundleSTP']= "";
		$bundleProduct['BundleCoverOpted']= "";
		$bundleProduct['BundlePolicyNo']= !empty($masterPolicy) ? $masterPolicy : "";
		$bundleProduct['TotalSumInsured']= $sumInsured;

		$bundleProduct['BundleMember'] = $policyMemberData;						 
		// Get And Create Policy Member Data Array End						 
		
		$memberInsertationArray['LeadId'] = $crmLeadId;
		$memberArrayData = array("MemberAge"=>$empAge,
                                "MemberId"=>!empty($empDetailsArray['emp_id']) ? $empDetailsArray['emp_id'] : "",
                                "MemberName"=>!empty($empDetailsArray['customer_name']) ? $empDetailsArray['customer_name'] : "",
                                "MemberPlanName"=>"",
                                "MemberPremium"=>$premiumAmount,
                                "MemberScheme"=>"",
                                "MemberSumInsured"=>$sumInsured,
								"OptionalCover"=>$optionalCoverArray
								);
								
		$memberInsertationArray['Member'] = array($memberArrayData);
		$memberInsertationArray['BundleProduct'] = array($bundleProduct);
		//echo "<pre>";print_r($memberInsertationArray);die();
		
		$jsonData = json_encode($memberInsertationArray );
		
		$request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_MEMBER_CRM_JSON"];
		$this->db->insert("logs_docs",$request_arr);
		//echo "<pre>";print_r($jsonData);die();
		
		//echo "==============================";
		//print_r($jsonData);
		// Prepare new cURL resource
		//$ch = curl_init('https://esblive.adityabirlahealth.com/ABHICL_CRM/Service1.svc/CreateMemberLead');
		$ch = curl_init('http://bizpre.adityabirlahealth.com/ABHICL_CRM/Service1.svc/CreateMemberLead');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "POST");
		curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
		curl_setopt($ch, CURLOPT_TIMEOUT , 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
		curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		 
		// Set HTTP Header for POST request 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData))
		);
		 
		// Submit the POST request
		$result = curl_exec($ch);
		
		if($result === false){
			$response = 'Curl error: ' . curl_error($ch);
		} else {
			$response = $result;
		}
		
		// Insert Data into Drop Off Log Start
		$this->db1->insert("drop_off_crm_data", [
        "type" => 'insert_member_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
		"requestresponse"=> $response
		]);
		// Insert Data into Drop Off Log End
		
		// return $result;
	}

    public function insertMemberCRMDropOffNew($emp_id,$crmLeadId){
        // Create LEAD CRM Start
        
        $this->db1 = $this->load->database('axis_retail',TRUE);
        
        // Get Employee Details
        $sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
        $empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
        $customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",$empDetailsArray['customer_name']) : "";
        
        // Get DOB
        $dateOfBirth = date("Y-m-d",strtotime($dob));
        $empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
        
        // DOB to Age Convertation Part Start
        $empAge = 0;
        if(!empty($empDOB)) {
            $today = date("Y-m-d");
            $diff = date_diff(date_create($empDOB), date_create($today));
            $empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;            
        }
        // DOB to Age Convertation Part End
        
        // Get Policy Details Start
        $queryGetProposalDetails = $this->db1->query("SELECT * FROM proposal WHERE emp_id = '".$emp_id."'")->row_array();
        $proposalNumber = !empty($queryGetProposalDetails['proposal_no']) ? $queryGetProposalDetails['proposal_no'] : "";
        $productId      = !empty($queryGetProposalDetails['product_id']) ? $queryGetProposalDetails['product_id'] : "";
        $sumInsured     = !empty($queryGetProposalDetails['sum_insured']) ? $queryGetProposalDetails['sum_insured'] : "";
        $premiumAmount      = !empty($queryGetProposalDetails['premium']) ? $queryGetProposalDetails['premium'] : "";
        // Get Policy Details End


        /* NEW Logic for D2C HP & HPI */
        $masterRelation = $this->db1->query("SELECT * FROM master_family_relation as mfr")->result_array();
        $masterRelations = array_column($masterRelation, 'fr_name', 'fr_id');
        $masterRelationsGeneric = array_column($masterRelation, 'reference_name', 'fr_id');

        $productSelected = $this->db1->query("SELECT ed.emp_id,ed.customer_name,etp.combination_id,etp.family_construct,etp.relationship_id,etp.sum_insured_amt,mpc.combination_policies FROM employee_details as ed, employee_to_product as etp,master_policy_combination as mpc WHERE ed.emp_id = etp.emp_id AND etp.combination_id = mpc.id AND ed.emp_id = '".$emp_id."'")->row_array();

        $relationshipIdArr = explode(',',$productSelected['relationship_id']);

        $memberAdded = $this->db1->query("SELECT 
           fr.family_relation_id AS family_relation_id,
           NULL AS family_detail_id,
           fr.family_id AS fr_id,
           0 AS sub_fr_id,
           fr.emp_id AS emp_id,
           ed.customer_name AS full_name,
           ed.gender AS gender,
           ed.bdate AS dob
           FROM 
           family_relation AS fr,
           employee_details AS ed

           WHERE
           fr.emp_id = ed.emp_id AND
           fr.emp_id = ".$emp_id." AND
           fr.family_id = 0 
           UNION ALL
           SELECT
           fr.family_relation_id AS family_relation_id,
           efd.family_id AS family_detail_id,
           efd.fr_id AS fr_id,
           efd.sub_fr_id AS sub_fr_id,
           fr.emp_id AS emp_id,
           efd.family_firstname AS full_name,
           efd.family_gender AS gender,
           efd.family_dob AS dob
           FROM 
           employee_family_details AS efd,
           family_relation AS fr
           WHERE
           efd.family_id = fr.family_id AND
           fr.emp_id = ".$emp_id."
           ORDER BY fr_id ASC;")->result_array();

        $policiesTaken = $productSelected['combination_policies'];
        $policyIdArr = explode(',',$policiesTaken);

        $i = 1;
        foreach ($policyIdArr as $key => $policyId) {
            $this->db1->select('epm.policy_member_id,epm.policy_detail_id,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_mem_dob,epm.policy_member_first_name,epm.policy_mem_gender,epm.sub_fr_id');
            $this->db1->from('employee_policy_member as epm');
            $this->db1->where_in('epm.family_relation_id',array_column($memberAdded, 'family_relation_id'));
            $this->db1->where('epm.policy_detail_id',$policyId);
            $policyMembersPerPolicy = $this->db1->get()->result_array();

            $this->db1->select('epd.*,pmst.master_policy_no');
            $this->db1->from('employee_policy_detail as epd');
            $this->db1->join('product_master_with_subtype as pmst','epd.product_name = pmst.id');
            $this->db1->where('epd.policy_detail_id',$policyId);
            $policyRelatedData = $this->db1->get()->row_array();

            $BundleFamilyConstructArr = [];
            $childCount = 0;
            foreach ($relationshipIdArr as $key => $value) {
                if(in_array($value,['0','1'])){
                    $BundleFamilyConstructArr[] = $masterRelationsGeneric[$value];
                } else if (in_array($value,['2','3']) && $policyRelatedData['policy_sub_type_id'] == '1') {
                    $childCount ++;
                    $BundleFamilyConstructArr[] = 'Kid'.$childCount;
                }
            }
            
            $memberRequestArr = [];
            $totalPremium = 0;
            foreach ($policyMembersPerPolicy as $key => $policyMemberPerPolicy) {
                if($policyRelatedData['sum_insured_type'] == 'familyGroup'){
                    $totalPremium = $policyMemberPerPolicy['policy_mem_sum_premium'];
                } else {
                    $totalPremium += $policyMemberPerPolicy['policy_mem_sum_premium'];
                }
                $calculatedAgeWithType = $this->Abc_m->getDiffInDates($policyMemberPerPolicy['policy_mem_dob'],date('d-m-Y'));
                $requestArr = [
                    "BundleMemberAge"=>$calculatedAgeWithType['diff'],
                    "BundleMemberId"=>$policyMemberPerPolicy['policy_member_id'],
                    "BundleMemberName"=>$policyMemberPerPolicy['policy_member_first_name'],
                    "BundleMemberPlan"=>"Test Plan",
                    "BundleMemberPremium"=>$policyMemberPerPolicy['policy_mem_sum_premium'],
                    "BundleMemberProduct"=>"Activ Health",
                    "BundleMemberRelationship"=>$masterRelations[$policyMemberPerPolicy['sub_fr_id']],
                    "BundleMemberSumInsured"=>$policyMemberPerPolicy['policy_mem_sum_insured']
                ];
                $memberRequestArr[] = $requestArr;
            }

            $fullRequestArr = [
                "LeadId" => $crmLeadId,
                "BundleProduct" => [
                    [
                        "BundleCoverOpted" => "SI",
                        "BundleFamilyConstruct" => implode('+',$BundleFamilyConstructArr),
                        "BundleFlag" => count($policyIdArr) > 1 ? 'Yes' : 'No',
                        "BundleMember"=> $memberRequestArr,
                        "BundlePolicyNo" => $policyRelatedData['master_policy_no'],
                        "BundlePremium" => $totalPremium,
                        "BundleProduct" => "Activ Health",
                        "BundleSTP" => count($policyIdArr) > 1 ? 'Yes' : 'No',
                        "TotalSumInsured" => $productSelected['sum_insured_amt']
                    ]
                ]
            ];
            // print_pre(json_encode($fullRequestArr));
            $jsonData = json_encode($fullRequestArr);

            // Calling API
            $ch = curl_init('http://bizpre.adityabirlahealth.com/ABHICL_CRM/Service1.svc/CreateMemberLead');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "POST");
            curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
            curl_setopt($ch, CURLOPT_TIMEOUT , 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
             
            // Set HTTP Header for POST request 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData))
            );
            // Submit the POST request
            $result = curl_exec($ch);
            if($result === false){
                $response = 'Curl error: ' . curl_error($ch);
            } else {
                $response = $result;
            }

            $request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_MEMBER_CRM_JSON_".$i, "product_id" => $empDetailsArray['product_id']];
            $this->db->insert("logs_docs",$request_arr);

            // Insert Data into Drop Off Log Start
            $this->db1->insert("drop_off_crm_data", [
                "type" => 'insert_member_crm_'.$i,
                "jsondata" => $jsonData,
                "leadid" => $empDetailsArray['lead_id'],
                "created_date" => date("Y-m-d h:i:s"),
                "requestresponse"=> $response
            ]);

            $i++;
        }
    }

    public function insertMemberCRMDropOffabc($emp_id,$crmLeadId){
        // Create LEAD CRM Start
        
        $this->db1 = $this->load->database('axis_retail',TRUE);
        
        // Get Employee Details
        $sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
        $empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
        $customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",$empDetailsArray['customer_name']) : "";
        
        // Get DOB
        $dateOfBirth = date("Y-m-d",strtotime($dob));
        $empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
        
        // DOB to Age Convertation Part Start
        $empAge = 0;
        if(!empty($empDOB)) {
            $today = date("Y-m-d");
            $diff = date_diff(date_create($empDOB), date_create($today));
            $empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;            
        }
        // DOB to Age Convertation Part End
        
        // Get Policy Details Start
        $queryGetProposalDetails = $this->db1->query("SELECT * FROM proposal WHERE emp_id = '".$emp_id."'")->row_array();
        $proposalNumber = !empty($queryGetProposalDetails['proposal_no']) ? $queryGetProposalDetails['proposal_no'] : "";
        $productId      = !empty($queryGetProposalDetails['product_id']) ? $queryGetProposalDetails['product_id'] : "";
        $sumInsured     = !empty($queryGetProposalDetails['sum_insured']) ? $queryGetProposalDetails['sum_insured'] : "";
        $premiumAmount      = !empty($queryGetProposalDetails['premium']) ? $queryGetProposalDetails['premium'] : "";
        // Get Policy Details End
        
        // Get Product Details Start
        $productName = "";
            
        if(!empty($productId)) {
            $queryGetProposalDetails = $this->db1->query("SELECT * FROM proposal WHERE emp_id = '".$emp_id."'")->row_array();
            $proposalNumber = !empty($queryGetProposalDetails['proposal_no']) ? $queryGetProposalDetails['proposal_no'] : "";
            
            $prodIdMain = !empty($queryGetProposalDetails['product_id']) ? $queryGetProposalDetails['product_id'] : "";
            $productData = $this->db1->query("SELECT * FROM product_master_with_subtype WHERE policy_parent_id = '".$prodIdMain."'")->row_array();
            $productName = !empty($productData['product_name']) ? $productData['product_name'] : "";
            $masterPolicy = !empty($productData['master_policy_no']) ? $productData['master_policy_no'] : "";
        } else {
            $productData = $this->db1->query("SELECT * FROM product_master_with_subtype")->row_array();
            $productName = !empty($productData['product_name']) ? $productData['product_name'] : "";
            $masterPolicy = !empty($productData['master_policy_no']) ? $productData['master_policy_no'] : "";
        }
        
        // Get Product Details End
        
        $memberInsertationArray = array();
        $optionalCoverArray     = array();
        $bundleProduct          = array();
        $bundleMember           = array();
        
        // Get And Create Policy Member Data Array Start
        $queryGetProposalMemberDetailsObj = $this->db1->query("SELECT EPM.policy_member_id, EPM.policy_member_first_name, EPM.age, EPM.policy_mem_sum_insured, EPM.policy_mem_sum_premium, EPM.family_type_name, FR.family_id FROM employee_policy_member AS EPM, family_relation AS FR WHERE EPM.family_relation_id = FR.family_relation_id AND FR.emp_id = '".$emp_id."'")->result();

        $sumInsured = "";
        $premiumAmount = "";
        $familyTypeName = "";
        foreach($queryGetProposalMemberDetailsObj as $key=>$mainMember) {
            
            $memberRelation = $this->db1->query("SELECT fr_name FROM master_family_relation WHERE fr_id = '".$mainMember->family_id."'")->row_array();
            
            if(empty($memberRelation['fr_name'])) {
                $memberFRID = $this->db1->query("SELECT fr_id FROM employee_family_details WHERE family_id = '".$mainMember->family_id."'")->row_array();
                
                if(!empty($memberFRID['fr_id'])) {
                    $memberRelation = $this->db1->query("SELECT fr_name FROM master_family_relation WHERE fr_id = '".$memberFRID['fr_id']."'")->row_array();
                }
            }
            
            $sumInsured = !empty($mainMember->policy_mem_sum_insured) ? $mainMember->policy_mem_sum_insured : "";
            $premiumAmount = !empty($mainMember->policy_mem_sum_premium) ? $mainMember->policy_mem_sum_premium : "";
            
            if($mainMember->family_id == 0) {
                $familyTypeName = str_replace("+",", ",$mainMember->family_type_name);
            }
            
            $policyMemberData[] = array( "BundleMemberId"=> !empty($mainMember->policy_member_id) ? $mainMember->policy_member_id : "",
                                    "BundleMemberName"=> !empty($mainMember->policy_member_first_name) ? $mainMember->policy_member_first_name : "",
                                    "BundleMemberAge"=> !empty($mainMember->age) ? $mainMember->age : "",
                                    "BundleMemberProduct"=> $productName,
                                    "BundleMemberPlan"=> "",
                                    "BundleMemberPremium"=> $premiumAmount,
                                    "BundleMemberSumInsured"=> $sumInsured,
                                    "BundleMemberRelationship"=> !empty($memberRelation['fr_name']) ? $memberRelation['fr_name'] : ""
                                 );
        }
        
        
        $bundleProduct['BundleFlag']= "";
        $bundleProduct['BundleFamilyConstruct']= $familyTypeName;
        $bundleProduct['BundleProduct']= $productName;
        $bundleProduct['BundlePremium']= $premiumAmount;
        $bundleProduct['BundleSTP']= "";
        $bundleProduct['BundleCoverOpted']= "";
        $bundleProduct['BundlePolicyNo']= !empty($masterPolicy) ? $masterPolicy : "";
        $bundleProduct['TotalSumInsured']= $sumInsured;

        $bundleProduct['BundleMember'] = $policyMemberData;                      
        // Get And Create Policy Member Data Array End                       
        
        $memberInsertationArray['LeadId'] = $crmLeadId;
        $memberArrayData = array("MemberAge"=>$empAge,
                                "MemberId"=>!empty($empDetailsArray['emp_id']) ? $empDetailsArray['emp_id'] : "",
                                "MemberName"=>!empty($empDetailsArray['customer_name']) ? $empDetailsArray['customer_name'] : "",
                                "MemberPlanName"=>"",
                                "MemberPremium"=>$premiumAmount,
                                "MemberScheme"=>"",
                                "MemberSumInsured"=>$sumInsured,
                                "OptionalCover"=>$optionalCoverArray
                                );
                                
        $memberInsertationArray['Member'] = array($memberArrayData);
        $memberInsertationArray['BundleProduct'] = array($bundleProduct);
        //echo "<pre>";print_r($memberInsertationArray);die();
        
        $jsonData = json_encode($memberInsertationArray );
        
        $request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_MEMBER_CRM_JSON"];
        $this->db->insert("logs_docs",$request_arr);
        //echo "<pre>";print_r($jsonData);die();
        
        //echo "==============================";
        //print_r($jsonData);
        // Prepare new cURL resource
        //$ch = curl_init('https://esblive.adityabirlahealth.com/ABHICL_CRM/Service1.svc/CreateMemberLead');
        $ch = curl_init('http://bizpre.adityabirlahealth.com/ABHICL_CRM/Service1.svc/CreateMemberLead');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "POST");
        curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
        curl_setopt($ch, CURLOPT_TIMEOUT , 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
         
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
         
        // Submit the POST request
        $result = curl_exec($ch);
        
        if($result === false){
            $response = 'Curl error: ' . curl_error($ch);
        } else {
            $response = $result;
        }
        
        // Insert Data into Drop Off Log Start
        $this->db1->insert("drop_off_crm_data", [
        "type" => 'insert_member_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
        "requestresponse"=> $response
        ]);
        // Insert Data into Drop Off Log End
        
        // return $result;
    }
	
	/*
	* This Function is Use to Add Lead Imto AXIS CRM
	* Created By Shardul Kulkarni<shardul.kulkarni@fyntune.com> 
	* Created Date : 27-Aug-2020
	*/
	public function createCRMLeadDropOff_bk_160821($emp_id) {
		// Create LEAD CRM Start
		// $emp_id = '2602';die;
		
		$this->db1 = $this->load->database('axis_retail',TRUE);
		
		// Get Employee Details
		$sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
		$empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
		
		$customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",trim($empDetailsArray['customer_name'])) : "";
		
		// Get DOB
		$dateOfBirth = date("Y-m-d",strtotime($dob));
		$empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
		$empAge = 0;
		// DOB to Age Convertation Part Start
		if(!empty($empDOB)) {
			$today = date("Y-m-d");
			$diff = date_diff(date_create($empDOB), date_create($today));
			$empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;			
		}
		// DOB to Age Convertation Part End
		
		
		// Customer JSON Data
		$jsonData = !empty($empDetailsArray['json_qote']) ? json_decode($empDetailsArray['json_qote'], true) : "";
		
		// Get Drop Off Long URL Start
		$sqlStr = "select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id,pmws.policy_parent_id
		FROM employee_details as ed INNER JOIN user_activity as ua
		ON ed.emp_id = ua.emp_id AND ua.emp_id = $emp_id
		INNER JOIN product_master_with_subtype as pmws ON ed.product_id = pmws.product_code";
		
		$aResult = $this->db1->query($sqlStr)->row_array();
		$activityType = $aResult['type'];
		// Activity Stage
		$activityStage = "";
		$rating = '';
        switch ($activityType) {
            case 1:
                $activityStage = "At know your premium page";
                $rating = "Cold";
                break;
            case 2:
                $activityStage = "At member enrollment page";
                $rating = "Warm";
                break;  
            case 3:
                $activityStage = "At review page";
                $rating = "Hot";
                break;  
            case 4:
                $activityStage = "Redirected to payment page";
                $rating = "Very Hot";
                break;  
            case 5:
                $activityStage = "At payment page";
                $rating = "Super-Hot";
                break; 
            case 6:
                $activityStage = "At thank you page";
                $rating = "Won";
                break;      
        }
		
		switch ($activityType) {
			case 1:
				$longUrl = base_url('retail_dashboard');
				break;
			case ($activityType == 2 || $activityType == 3):
				$longUrl = base_url('retail_enrollment');
				break;
			case 4:
				$longUrl = base_url('api/payment_redirection');
				break;
			case ($activityType == 5 || $activityType == 6):
				$longUrl = base_url('payment_success_view_call');
				break;
		}
		// Get Drop Off Long URL End
		$premium_amt = 0;
		$premium_amount = [];
		// Get Premium Start
		//$query_premium = $this->db1->query("select epm.policy_mem_sum_premium,epm.policy_mem_sum_insured,epm.familyConstruct from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$emp_id)->row_array();
		$query_premiums = $this->db1->query("(SELECT epm.policy_mem_sum_premium as premium,epm.policy_mem_sum_insured from family_relation as fr,employee_policy_member as epm,employee_policy_detail AS epd where epd.policy_detail_id = epm.policy_detail_id AND fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id'  and epd.policy_sub_type_id = 1 order BY epd.family_relation_id ASC LIMIT 1) UNION ALL (SELECT SUM(epm.policy_mem_sum_premium) as premium,epm.policy_mem_sum_insured  from family_relation as fr,employee_policy_member as epm,employee_policy_detail AS epd where epd.policy_detail_id = epm.policy_detail_id AND fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id'  and epd.policy_sub_type_id != 1 order BY epd.family_relation_id )")->result_array();
		foreach($query_premiums as $premium_data)
		{
			$premium_amt+= $premium_data['premium'];
			array_push($premium_amount,$premium_amt);
		}
		
		$premium = !empty($premium_amount[0]) ? $premium_amount[0] : 0;
		$familyConstruct = !empty($query_premium['familyConstruct']) ? $query_premium['familyConstruct'] : 0;
		$sumInsured = !empty($query_premium['policy_mem_sum_insured']) ? $query_premium['policy_mem_sum_insured'] : 0;
		// Get Premium End
		
		// Get Proposal Details Start
		$queryProposal = "SELECT * FROM proposal WHERE emp_id = ".$emp_id;
		$query_proposal = $this->db1->query($queryProposal)->row_array();
		$proposalNumber = !empty($query_proposal['proposal_no']) ? $query_proposal['proposal_no'] : "";
		$proposalStatus = !empty($query_proposal['status']) ? $query_proposal['status'] : "";
		// Get Proposal Details End
		
		// Get Certificate Details Start
		$certificateNumber = NULL;
		if(!empty($proposalNumber)) {
			$queryCertificateDetails = "SELECT * FROM api_proposal_response WHERE proposal_no_lead = '".$proposalNumber."'";
			$query_CertificateDetails = $this->db1->query($queryCertificateDetails)->row_array();
			$certificateNumber = !empty($query_CertificateDetails['certificate_number']) ? $query_CertificateDetails['certificate_number'] : "";
		}
		// Get Certificate Details End
		
		// Get Nominee Details Start
		$queryNomineeDetails = "SELECT mpn.nominee_fname,mn.nominee_type FROM member_policy_nominee as mpn, master_nominee as mn WHERE mpn.fr_id = mn.nominee_id AND emp_id = ".$emp_id;
		$query_nominee = $this->db1->query($queryNomineeDetails)->row_array();
		$nomineeName = !empty($query_nominee['nominee_fname']) ? $query_nominee['nominee_fname'] : "";
		$nomineeRelation = !empty($query_nominee['nominee_type']) ? $query_nominee['nominee_type'] : "";
		// Get Nominee Details End
		
		// First Name, Last Name, Middle name
		$firstName = !empty($customerName[0]) ? $customerName[0] : "";
		$lastName = !empty($customerName) && count($customerName) > 2 ? $customerName[2] : $customerName[1];
		$middleName = "";
		$lastname = "";
		if(!empty($customerName) && count($customerName) > 2) {
			$middleName = !empty($customerName[1]) ? $customerName[1] : ".";
		} 
		
		$pinCode = !empty($empDetailsArray['emp_pincode']) ? $empDetailsArray['emp_pincode'] : "";
		
		// Create Lead API Array
		$leadCreateArray = array(
						"AADHARNO"=>!empty($empDetailsArray['adhar']) ? $empDetailsArray['adhar'] : "",
						"PINCODE"=>$pinCode,
						"ACTIVITYDESCRIPTION"=>"",
						"ACTIVITYSUBECT"=>$activityStage,
						"ACTIVITYTYPE"=>NULL,
						"ADDRESSLINE1"=> !empty($empDetailsArray['address']) ? $empDetailsArray['address'] : "",
						"ADDRESSLINE2"=>"",
						"ADDRESSLINE3"=>"",
						"AGE"=>$empAge,
						"ALCOHOLTABBACOCONSUMPTION"=>"",
						"APPLICATIONNO"=>"",
						"PartnerLeadId"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
						"ATTACHMENTS"=>array(),
						"Adgroup"=>"",
						"AffiliateDiscountFlag"=>"",
						"CITY"=>!empty($empDetailsArray['emp_city']) ? $empDetailsArray['emp_city'] : "",
						"CONTACTNUMBER"=>"",
						"COPAYMENTWAIVER"=>"",
						"COVER"=>"Self",
						"CUSTOMERTYPE"=>"",
						"DATEOFBIRTH"=>$empDOB,
						"DEDUCTIBLES"=>"",
						"DRIVINGLICENSENO"=>"",
						"EMAIL"=>!empty($empDetailsArray['email']) ? $empDetailsArray['email'] : "",
						"MOBILE"=>!empty($empDetailsArray['mob_no']) ? substr($empDetailsArray['mob_no'], -10) : "",
						"EXISTINGINSURANCE"=>"",
						"EXISTINGINSURANCECOVER"=>"",
						"EXISTINGINSURER"=>"",
						"EmployeeDiscountFlag"=>"",
						"EmployeeID"=>"",
						"FAMILYCONSTRUCT"=>$familyConstruct,
						"FIRSTNAME"=>$firstName,
						"MIDDLENAME" => $middleName,
						"LASTNAME"=>$lastName,						
						"GCLID"=>"",
						"GENDER"=>!empty($empDetailsArray['gender']) ? $empDetailsArray['gender'] : "",
						"HEIGHT"=>"",
						"HOSPITALCASHBENEFIT"=>"",
						"AutoRenewalFlag"=>!empty($empDetailsArray['auto_renewal']) ? $empDetailsArray['auto_renewal'] : "",
						"Rating"=> $rating,
						"IDNUMBER"=>"",
						"IDTYPE"=>NULL,
						"INTERMEDIARYCODE"=>!empty($empDetailsArray['imd_code']) ? $empDetailsArray['imd_code'] : "",
						"Keyword"=>"",
						"LEADCREATEDBY"=>"",
						"LEADOWNER"=>!empty($empDetailsArray['LEADOWNERID']) ? $empDetailsArray['LEADOWNERID'] : "",
						"LEADREFERREDBY"=>"",
						"LEADREFERREDBYID"=>"",
						"LEADSTAGE"=> $activityStage,
						"LEADSTATUS"=>"",
						"LEADTYPE"=>"Affinity",
						"LONGURL"=>$longUrl,
						"LeadId"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
						"LemniskId"=>"",
						"MATERNITYANDVACINATION"=>"",
						"MIDDLENAME"=>!empty($empDetailsArray['emp_middlename']) ? $empDetailsArray['emp_middlename'] : "",
						//"MOBILE"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
						"NOMINEENAME"=>$nomineeName,
						"NOMINEERELATION"=>$nomineeRelation,
						"NOTESDESCRIPTION"=>"",
						"NOTESTITLE"=>"",
						"OPDEXPENSES"=>"",
						"OneABCData"=>array(
							"ABCinId"=>"",
							"AddressLine3"=>"",
							"Anniversary"=>"",
							"CallBackDateTime"=>"",
							"Category"=>"",
							"Comments"=>"",
							"CreatedByInSource"=>"",
							"CustOKForCallOut"=>"",
							"Description"=>"",
							"GoldenId"=>"",
							"Income"=>"",
							"LOBPresence"=>"",
							"LeadQualityCode"=>"",
							"LeadSubSource"=>"Axis D2C",
							"RefDepartment"=>"",
							"RefEmpEmail"=>"",
							"RefEmpId"=>!empty($empDetailsArray['ref1']) ? $empDetailsArray['ref1'] : "",
							"RefEmpName"=>"",
							"RefEmpPhone"=>"",
							"Remarks"=>"",
							"SourceBusinessName"=>NULL,
							"SourceFunctionName"=>NULL,
							"SourceLeadId"=>"",
							"SourceOwnerId"=>"",
							"SourceOwnerName"=>"",
							"SourceUserEmail"=>"",
							"SourceUserId"=>"",
							"SourceUserLOB"=>"",
							"SourceUserLocation"=>"",
							"SourceUserName"=>"",
							"SpecificProductIntrest"=>"",
							"SubSource"=>"",
							"Telephone"=>""
						),
						"PAN"=>!empty($empDetailsArray['pancard']) ? $empDetailsArray['pancard'] : "",
						"PASSPORTNO"=>"",
						"PAYMENTAMOUNT"=>$premium,
						"PAYMENTSTATUS"=>$proposalStatus,
						//"POLICYNO"=>!empty($proposalNumber) ? $proposalNumber : NULL,
						"POLICYNO"=>$certificateNumber,
						"PREEXISTINGDISEASEMEDICALCONDITION"=>"",
						"PREFERREDCONTACTIBLETIME"=>NULL,
						"PREFERREDMODEOFCONTACT"=>NULL,
						"PREMIUM"=>$premium,
						"PRODUCT"=>"Group Activ Health ",
						"PROPOSALTYPE"=>!empty($activityType) && $activityType == 6 ? "STP" : "",
						"QUOTENUMBER"=>"",
						"REFERENCENUMBER"=>"",
						"RESIDENTPHONE"=>"",
						"ROOMTYPE"=>"",
						"SALUTATION"=>!empty($empDetailsArray['salutation']) ? $empDetailsArray['salutation'] : "",
						"SHORTURL"=>"",
						"SOURCE"=>"Axis D2C",
						"SOURCEBUSINESSNAME"=>NULL,
						"SOURCEFUNCTION"=>NULL,
						"STATE"=>!empty($empDetailsArray['emp_state']) ? $empDetailsArray['emp_state'] : "",
						"SUBSOURCE"=>"Axis D2C",
						"SUMINSURED"=>$sumInsured,
						"Salary"=>!empty($empDetailsArray['annual_income']) ? $empDetailsArray['annual_income'] : "",
						"TENURE"=>"",
						"URL"=>"",
						"UTMContent"=>"",
						"VERIFIED"=>"",
						"WEIGHT"=>"",
						"ZONE"=>NULL,
						"LEADSUBSOURCE"=>"Axis D2C"
				   );
			   		   
		$jsonData = json_encode($leadCreateArray);
		
		$request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_LEAD_CRM_JSON", "product_id"=>"R05"];
		$this->db->insert("logs_docs",$request_arr);
		
		// Prepare new cURL resource
		//$ch = curl_init('https://esblive.adityabirlahealth.com/ABHICL_CRM/Service1.svc/LeadCreation');
		$ch = curl_init('http://10.1.226.32/ABHICL_CRM/Service1.svc/LeadCreation');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		 
		// Set HTTP Header for POST request 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData))
		);
		 
		// Submit the POST request
		$result = curl_exec($ch);
		
		if($result === false){
			$response =  'Curl error: ' . curl_error($ch);
		} else {
			$response = $result;
		}
		
		// Insert Data into Drop Off Log Start
		$this->db1->insert("drop_off_crm_data", [
        "type" => 'create_lead_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
		"requestresponse"=> $response
		]);
		// Insert Data into Drop Off Log End
		
		return $response;
	}

    /*
    * This Function is Use to Add Lead Imto AXIS CRM
    * Created By Shardul Kulkarni<shardul.kulkarni@fyntune.com> 
    * Created Date : 27-Aug-2020
    */
    public function createCRMLeadDropOff($emp_id) {
        // Create LEAD CRM Start
        // $emp_id = '2602';
        
        $this->db1 = $this->load->database('axis_retail',TRUE);
        $empIdDec = encrypt_decrypt_password($emp_id);
        // Get Employee Details
        $sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
        $empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
        
        $customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",trim($empDetailsArray['customer_name'])) : "";
        
        // Get DOB
        $dateOfBirth = date("Y-m-d",strtotime($dob));
        $empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
        $empAge = 0;
        // DOB to Age Convertation Part Start
        if(!empty($empDOB)) {
            $today = date("Y-m-d");
            $diff = date_diff(date_create($empDOB), date_create($today));
            $empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;            
        }
        // DOB to Age Convertation Part End
        
        
        // Customer JSON Data
        $jsonData = !empty($empDetailsArray['json_qote']) ? json_decode($empDetailsArray['json_qote'], true) : "";
        
        // Get Drop Off Long URL Start
        $sqlStr = "select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id,pmws.policy_parent_id
        FROM employee_details as ed INNER JOIN user_activity as ua
        ON ed.emp_id = ua.emp_id AND ua.emp_id = $emp_id
        LEFT JOIN product_master_with_subtype as pmws ON ed.product_id = pmws.product_code";
        
        $aResult = $this->db1->query($sqlStr)->row_array();
        $activityType = $aResult['type'];
        // Activity Stage
        $activityStage = "";
        $rating = '';
        switch ($activityType) {
            case 1:
                $activityStage = "At know your premium page";
                $rating = "Cold";
                break;
            case 2:
                $activityStage = "At member enrollment page";
                $rating = "Warm";
                break;  
            case 3:
                $activityStage = "At review page";
                $rating = "Hot";
                break;  
            case 4:
                $activityStage = "Redirected to payment page";
                $rating = "Very Hot";
                break;  
            case 5:
                $activityStage = "At payment page";
                $rating = "Super-Hot";
                break; 
            case 6:
                $activityStage = "At thank you page";
                $rating = "Won";
                break;      
        }
        
        switch ($activityType) {
            case 1:
                $longUrl = base_url('retail_dashboard');
                break;
            case ($activityType == 2 || $activityType == 3):
                $longUrl = base_url('retail_enrollment');
                break;
            case 4:
                $longUrl = base_url('payment_redirection_retail/'.$empIdDec);
                break;
            case ($activityType == 5 || $activityType == 6):
                $longUrl = base_url('payment_success_view_call');
                break;
        }
        // Get Drop Off Long URL End
        $premium_amt = 0;
		$premium_amount = [];
		//$sum_insured = 0;
		//$family_construct = 0;
        // Get Premium Start
		
		    
	
       // $query_premium = $this->db1->query("select epm.policy_mem_sum_premium,epm.policy_mem_sum_insured,epm.familyConstruct from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$emp_id)->row_array();
        
        //$premium = !empty($query_premium['policy_mem_sum_premium']) ? $query_premium['policy_mem_sum_premium'] : 0;
        $query_premiums = $this->db1->query("(SELECT epm.policy_mem_sum_premium as premium,epm.policy_mem_sum_insured,epm.familyConstruct from family_relation as fr,employee_policy_member as epm,employee_policy_detail AS epd where epd.policy_detail_id = epm.policy_detail_id AND fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id'  and epd.policy_sub_type_id = 1 order BY epd.family_relation_id ASC LIMIT 1) UNION ALL (SELECT SUM(epm.policy_mem_sum_premium) as premium,epm.policy_mem_sum_insured,epm.familyConstruct from family_relation as fr,employee_policy_member as epm,employee_policy_detail AS epd where epd.policy_detail_id = epm.policy_detail_id AND fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id'  and epd.policy_sub_type_id != 1 order BY epd.family_relation_id )")->result_array();

		if(!empty($query_premiums)){
		foreach($query_premiums as $premium_data)
		{
			array_push($premium_amount,$premium_data['premium']);
		
		}
		
		}
		$premium_amt = array_sum($premium_amount);
		$premium = !empty($premium_amt) ? $premium_amt : 0;
		$familyConstruct = !empty($query_premiums[0]['familyConstruct']) ? $query_premiums[0]['familyConstruct'] : 0;
		$sumInsured = !empty($query_premiums[0]['policy_mem_sum_insured']) ? $query_premiums[0]['policy_mem_sum_insured'] : 0;
		
		//deductable amount
		$query_deduct = $this->db1->query("select deductible_amount from employee_to_product where emp_id = '$emp_id'")->row_array();
        // Get Premium End
        $deductAmt = !empty($query_deduct['deductible_amount']) ? $query_deduct['deductible_amount'] : 0;

        // Get Proposal Details Start
        $queryProposal = "SELECT * FROM proposal WHERE emp_id = ".$emp_id;
        $query_proposal = $this->db1->query($queryProposal)->row_array();
        $proposalNumber = !empty($query_proposal['proposal_no']) ? $query_proposal['proposal_no'] : "";
        $proposalStatus = !empty($query_proposal['status']) ? $query_proposal['status'] : "";
        // Get Proposal Details End
        
        // Get Certificate Details Start
        $certificateNumber = NULL;
        if(!empty($proposalNumber)) {
            $queryCertificateDetails = "SELECT * FROM api_proposal_response WHERE proposal_no_lead = '".$proposalNumber."'";
            $query_CertificateDetails = $this->db1->query($queryCertificateDetails)->row_array();
            $certificateNumber = !empty($query_CertificateDetails['certificate_number']) ? $query_CertificateDetails['certificate_number'] : "";
        }
        // Get Certificate Details End
        
        // Get Nominee Details Start
        $queryNomineeDetails = "SELECT mpn.nominee_fname,mn.nominee_type FROM member_policy_nominee as mpn, master_nominee as mn WHERE mpn.fr_id = mn.nominee_id AND emp_id = ".$emp_id;
        $query_nominee = $this->db1->query($queryNomineeDetails)->row_array();
        $nomineeName = !empty($query_nominee['nominee_fname']) ? $query_nominee['nominee_fname'] : "";
        $nomineeRelation = !empty($query_nominee['nominee_type']) ? $query_nominee['nominee_type'] : "";
        // Get Nominee Details End
        
        // First Name, Last Name, Middle name
        $firstName = !empty($customerName[0]) ? $customerName[0] : "";
        $lastName = !empty($customerName) && count($customerName) > 2 ? $customerName[2] : $customerName[1];
        $middleName = "";
        $lastname = "";
        if(!empty($customerName) && count($customerName) > 2) {
            $middleName = !empty($customerName[1]) ? $customerName[1] : ".";
        } 
        
        $pinCode = !empty($empDetailsArray['emp_pincode']) ? $empDetailsArray['emp_pincode'] : "";
        
        // Create Lead API Array
        $leadCreateArray = array(
                        "AADHARNO"=>!empty($empDetailsArray['adhar']) ? $empDetailsArray['adhar'] : "",
                        "PINCODE"=>$pinCode,
                        "ACTIVITYDESCRIPTION"=>"",
                        "ACTIVITYSUBECT"=>$activityStage,
                        "ACTIVITYTYPE"=>NULL,
                        "ADDRESSLINE1"=> !empty($empDetailsArray['address']) ? $empDetailsArray['address'] : "",
                        "ADDRESSLINE2"=>"",
                        "ADDRESSLINE3"=>"",
                        "AGE"=>$empAge,
                        "ALCOHOLTABBACOCONSUMPTION"=>"",
                        "APPLICATIONNO"=>"",
                        "PartnerLeadId"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "ATTACHMENTS"=>array(),
                        "Adgroup"=>"",
                        "AffiliateDiscountFlag"=>"",
                        "CITY"=>!empty($empDetailsArray['emp_city']) ? $empDetailsArray['emp_city'] : "",
                        "CONTACTNUMBER"=>"",
                        "COPAYMENTWAIVER"=>"",
                        "COVER"=>"Self",
                        "CUSTOMERTYPE"=>"",
                        "DATEOFBIRTH"=>$empDOB,
                        "DEDUCTIBLES"=> $deductAmt,
                        "DRIVINGLICENSENO"=>"",
                        "EMAIL"=>!empty($empDetailsArray['email']) ? $empDetailsArray['email'] : "",
                        "MOBILE"=>!empty($empDetailsArray['mob_no']) ? substr($empDetailsArray['mob_no'], -10) : "",
                        "EXISTINGINSURANCE"=>"",
                        "EXISTINGINSURANCECOVER"=>"",
                        "EXISTINGINSURER"=>"",
                        "EmployeeDiscountFlag"=>"",
                        "EmployeeID"=>"",
                        "FAMILYCONSTRUCT"=>$familyConstruct,
                        "FIRSTNAME"=>$firstName,
                        "MIDDLENAME" => $middleName,
                        "LASTNAME"=>$lastName,                      
                        "GCLID"=>"",
                        "GENDER"=>!empty($empDetailsArray['gender']) ? $empDetailsArray['gender'] : "",
                        "HEIGHT"=>"",
                        "HOSPITALCASHBENEFIT"=>"",
                        "AutoRenewalFlag"=>!empty($empDetailsArray['auto_renewal']) ? $empDetailsArray['auto_renewal'] : "",
                        "Rating"=> $rating,
                        "IDNUMBER"=>"",
                        "IDTYPE"=>NULL,
                        "INTERMEDIARYCODE"=>!empty($empDetailsArray['imd_code']) ? $empDetailsArray['imd_code'] : "",
                        "Keyword"=>"",
                        "LEADCREATEDBY"=>"",
                        "LEADOWNER"=>!empty($empDetailsArray['LEADOWNERID']) ? $empDetailsArray['LEADOWNERID'] : "",
                        "LEADREFERREDBY"=>"",
                        "LEADREFERREDBYID"=>"",
                        "LEADSTAGE"=> $activityStage,
                        "LEADSTATUS"=>"",
                        "LEADTYPE"=>"Affinity",
                        "LONGURL"=>$longUrl,
                        "LeadId"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "LemniskId"=>"",
                        "MATERNITYANDVACINATION"=>"",
                        "MIDDLENAME"=>!empty($empDetailsArray['emp_middlename']) ? $empDetailsArray['emp_middlename'] : "",
                        //"MOBILE"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "NOMINEENAME"=>$nomineeName,
                        "NOMINEERELATION"=>$nomineeRelation,
                        "NOTESDESCRIPTION"=>"",
                        "NOTESTITLE"=>"",
                        "OPDEXPENSES"=>"",
                        "OneABCData"=>array(
                            "ABCinId"=>"",
                            "AddressLine3"=>"",
                            "Anniversary"=>"",
                            "CallBackDateTime"=>"",
                            "Category"=>"",
                            "Comments"=>"",
                            "CreatedByInSource"=>"",
                            "CustOKForCallOut"=>"",
                            "Description"=>"",
                            "GoldenId"=>"",
                            "Income"=>"",
                            "LOBPresence"=>"",
                            "LeadQualityCode"=>"",
                            "LeadSubSource"=>"Axis D2C",
                            "RefDepartment"=>"",
                            "RefEmpEmail"=>"",
                            "RefEmpId"=>!empty($empDetailsArray['ref1']) ? $empDetailsArray['ref1'] : "",
                            "RefEmpName"=>"",
                            "RefEmpPhone"=>"",
                            "Remarks"=>"",
                            "SourceBusinessName"=>NULL,
                            "SourceFunctionName"=>NULL,
                            "SourceLeadId"=>"",
                            "SourceOwnerId"=>"",
                            "SourceOwnerName"=>"",
                            "SourceUserEmail"=>"",
                            "SourceUserId"=>"",
                            "SourceUserLOB"=>"",
                            "SourceUserLocation"=>"",
                            "SourceUserName"=>"",
                            "SpecificProductIntrest"=>"",
                            "SubSource"=>"",
                            "Telephone"=>""
                        ),
                        "PAN"=>!empty($empDetailsArray['pancard']) ? $empDetailsArray['pancard'] : "",
                        "PASSPORTNO"=>"",
                        "PAYMENTAMOUNT"=>$premium,
                        "PAYMENTSTATUS"=>$proposalStatus,
                        //"POLICYNO"=>!empty($proposalNumber) ? $proposalNumber : NULL,
                        "POLICYNO"=>$certificateNumber,
                        "PREEXISTINGDISEASEMEDICALCONDITION"=>"",
                        "PREFERREDCONTACTIBLETIME"=>NULL,
                        "PREFERREDMODEOFCONTACT"=>NULL,
                        "PREMIUM"=>$premium,
                        "PRODUCT"=>"Group Activ Health ",
                        "PROPOSALTYPE"=>!empty($activityType) && $activityType == 6 ? "STP" : "",
                        "QUOTENUMBER"=>"",
                        "REFERENCENUMBER"=>"",
                        "RESIDENTPHONE"=>"",
                        "ROOMTYPE"=>"",
                        "SALUTATION"=>!empty($empDetailsArray['salutation']) ? $empDetailsArray['salutation'] : "",
                        "SHORTURL"=>"",
                        "SOURCE"=>"Axis D2C",
                        "SOURCEBUSINESSNAME"=>NULL,
                        "SOURCEFUNCTION"=>NULL,
                        "STATE"=>!empty($empDetailsArray['emp_state']) ? $empDetailsArray['emp_state'] : "",
                        "SUBSOURCE"=>"Axis D2C",
                        "SUMINSURED"=>$sumInsured,
                        "Salary"=>!empty($empDetailsArray['annual_income']) ? $empDetailsArray['annual_income'] : "",
                        "TENURE"=>"",
                        "URL"=>"",
                        "UTMContent"=>"",
                        "VERIFIED"=>"",
                        "WEIGHT"=>"",
                        "ZONE"=>NULL,
                        "LEADSUBSOURCE"=>"Axis D2C"
                   );
                       
        $jsonData = json_encode($leadCreateArray);
        
        // Prepare new cURL resource
        $ch = curl_init('http://10.1.226.32/ABHICL_CRM/Service1.svc/LeadCreation');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
         
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
         
        // Submit the POST request
        $result = curl_exec($ch);
        
        if($result === false){
            $response =  'Curl error: ' . curl_error($ch);
        } else {
            $response = $result;
        }

        $request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_LEAD_CRM_JSON", "product_id"=>$empDetailsArray['product_id']];
        $this->db->insert("logs_docs",$request_arr);
        
        // Insert Data into Drop Off Log Start
        $this->db1->insert("drop_off_crm_data", [
        "type" => 'create_lead_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
        "requestresponse"=> $response
        ]);
        // Insert Data into Drop Off Log End
        
        return $response;
    }

    /*
    * This Function is Use to Add Lead Imto AXIS CRM
    * Created By Shardul Kulkarni<shardul.kulkarni@fyntune.com> 
    * Created Date : 27-Aug-2020
    */
    public function createCRMLeadDropOffabc_bk($emp_id) {
        // Create LEAD CRM Start
        // $emp_id = '2602';
        $this->db1 = $this->load->database('axis_retail',TRUE);
        
        // Get Employee Details
        $sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
        $empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
        
        $customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",trim($empDetailsArray['customer_name'])) : "";
        
        // Get DOB
        $dateOfBirth = date("Y-m-d",strtotime($dob));
        $empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
        $empAge = 0;
        // DOB to Age Convertation Part Start
        if(!empty($empDOB)) {
            $today = date("Y-m-d");
            $diff = date_diff(date_create($empDOB), date_create($today));
            $empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;            
        }
        // DOB to Age Convertation Part End
        
        $policy_detail_id = $this->db->get_where("employee_product_details",array("emp_id" => $emp_id))->row_array();
        $policy_id = $policy_detail_id['policy_id'];
        // Customer JSON Data
        $jsonData = !empty($empDetailsArray['json_qote']) ? json_decode($empDetailsArray['json_qote'], true) : "";
        
        // Get Drop Off Long URL Start
        $sqlStr = "select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id
        FROM employee_details as ed INNER JOIN user_activity as ua
        ON ed.emp_id = ua.emp_id AND ua.emp_id = $emp_id";
        //echo $sqlStr;exit;
        $aResult = $this->db1->query($sqlStr)->row_array();
        $activityType = $aResult['type'];
        
        // Activity Stage
        $activityStage = "";
		$rating = '';
        switch ($activityType) {
            case 1:
                $activityStage = "at Product Page";
                $rating = "Cold";
                break;
            case 2:
                $activityStage = "at know your premium page";
                $rating = "Cold";
                break;
            case 3:
                $activityStage = "at member enrollement page";
                $rating = "Warm";
                break;  
            case 4:
                $activityStage = "at review page";
                $rating = "Hot";
                break;  
            case 5:
                $activityStage = "at redirected to payment page";
                $rating = "Very Hot";
                break;  
            case 6:
                $activityStage = "at payment page";
                $rating = "Super-Hot";
                break; 
            case 7:
                $activityStage = "at thank you page";
                $rating = "Won";
                break;      
        }
        
	
        switch ($activityType) {
            case 1:
                $longUrl = base_url('comprehensive_product_abc');
                break;
            case 2:
                $longUrl = base_url('quotes_abc/'.$policy_id);
                break;
            case 3:
                $longUrl = base_url('member_proposer_detail');
                break;
            case 4:
                $longUrl = base_url('member_detail_product_abc');
                break;
             case ($activityType == 5 || $activityType == 6):
                $longUrl = base_url('payment_redirection_abc');
                break;
            case 7:
                $longUrl = base_url('payment_success_view_call_abc/'.$emp_id);
                break;
        }
        // Get Drop Off Long URL End
        //echo $longUrl;exit;
        // Get Premium Start
        $query_premium = $this->db1->query("select SUM(epm.policy_mem_sum_premium) as policy_mem_sum_premium,epm.policy_mem_sum_insured,epm.familyConstruct from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$emp_id)->row_array();
        
        $premium = !empty($query_premium['policy_mem_sum_premium']) ? $query_premium['policy_mem_sum_premium'] : 0;
        $familyConstruct = !empty($query_premium['familyConstruct']) ? $query_premium['familyConstruct'] : 0;
        $sumInsured = !empty($query_premium['policy_mem_sum_insured']) ? $query_premium['policy_mem_sum_insured'] : 0;
        // Get Premium End
        
        // Get Proposal Details Start
        $queryProposal = "SELECT * FROM proposal WHERE emp_id = ".$emp_id;
        $query_proposal = $this->db1->query($queryProposal)->row_array();
        $proposalNumber = !empty($query_proposal['proposal_no']) ? $query_proposal['proposal_no'] : "";
        $proposalStatus = !empty($query_proposal['status']) ? $query_proposal['status'] : "";
        // Get Proposal Details End
        
        // Get Certificate Details Start
        $certificateNumber = NULL;
        if(!empty($proposalNumber)) {
            $queryCertificateDetails = "SELECT * FROM api_proposal_response WHERE proposal_no_lead = '".$proposalNumber."'";
            $query_CertificateDetails = $this->db1->query($queryCertificateDetails)->row_array();
            $certificateNumber = !empty($query_CertificateDetails['certificate_number']) ? $query_CertificateDetails['certificate_number'] : "";
        }
        // Get Certificate Details End
        
        // Get Nominee Details Start
        $queryNomineeDetails = "SELECT mpn.nominee_fname,mn.nominee_type FROM member_policy_nominee as mpn, master_nominee as mn WHERE mpn.fr_id = mn.nominee_id AND emp_id = ".$emp_id;
        $query_nominee = $this->db1->query($queryNomineeDetails)->row_array();
        $nomineeName = !empty($query_nominee['nominee_fname']) ? $query_nominee['nominee_fname'] : "";
        $nomineeRelation = !empty($query_nominee['nominee_type']) ? $query_nominee['nominee_type'] : "";
        // Get Nominee Details End
        
        // First Name, Last Name, Middle name
        $firstName = !empty($customerName[0]) ? $customerName[0] : "";
        $lastName = !empty($customerName) && count($customerName) > 2 ? $customerName[2] : $customerName[1];
        $middleName = "";
        $lastname = "";
        if(!empty($customerName) && count($customerName) > 2) {
            $middleName = !empty($customerName[1]) ? $customerName[1] : ".";
        } 
        
        $pinCode = !empty($empDetailsArray['emp_pincode']) ? $empDetailsArray['emp_pincode'] : "";
        
        // Create Lead API Array
        $leadCreateArray = array(
                        "AADHARNO"=>!empty($empDetailsArray['adhar']) ? $empDetailsArray['adhar'] : "",
                        "PINCODE"=>$pinCode,
                        "ACTIVITYDESCRIPTION"=>"",
                        "ACTIVITYSUBECT"=>$activityStage,
                        "ACTIVITYTYPE"=>NULL,
                        "ADDRESSLINE1"=> !empty($empDetailsArray['address']) ? $empDetailsArray['address'] : "",
                        "ADDRESSLINE2"=>"",
                        "ADDRESSLINE3"=>"",
                        "AGE"=>$empAge,
                        "ALCOHOLTABBACOCONSUMPTION"=>"",
                        "APPLICATIONNO"=>"",
                        "PartnerLeadId"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "ATTACHMENTS"=>array(),
                        "Adgroup"=>"",
                        "AffiliateDiscountFlag"=>"",
                        "CITY"=>!empty($empDetailsArray['emp_city']) ? $empDetailsArray['emp_city'] : "",
                        "CONTACTNUMBER"=>"",
                        "COPAYMENTWAIVER"=>"",
                        "COVER"=>"Self",
                        "CUSTOMERTYPE"=>"",
                        "DATEOFBIRTH"=>$empDOB,
                        "DEDUCTIBLES"=>"",
                        "DRIVINGLICENSENO"=>"",
                        "EMAIL"=>!empty($empDetailsArray['email']) ? $empDetailsArray['email'] : "",
                        "MOBILE"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "EXISTINGINSURANCE"=>"",
                        "EXISTINGINSURANCECOVER"=>"",
                        "EXISTINGINSURER"=>"",
                        "EmployeeDiscountFlag"=>"",
                        "EmployeeID"=>"",
                        "FAMILYCONSTRUCT"=>$familyConstruct,
                        "FIRSTNAME"=>$firstName,
                        "MIDDLENAME" => $middleName,
                        "LASTNAME"=>$lastName,                      
                        "GCLID"=>"",
                        "GENDER"=>!empty($empDetailsArray['gender']) ? $empDetailsArray['gender'] : "",
                        "HEIGHT"=>"",
                        "HOSPITALCASHBENEFIT"=>"",
                        "AutoRenewalFlag"=>!empty($empDetailsArray['auto_renewal']) ? $empDetailsArray['auto_renewal'] : "",
                        "Rating"=> "HOT",
                        "IDNUMBER"=>"",
                        "IDTYPE"=>NULL,
                        "INTERMEDIARYCODE"=>!empty($empDetailsArray['imd_code']) ? $empDetailsArray['imd_code'] : "",
                        "Keyword"=>"",
                        "LEADCREATEDBY"=>"",
                        "LEADOWNER"=>!empty($empDetailsArray['LEADOWNERID']) ? $empDetailsArray['LEADOWNERID'] : "",
                        "LEADREFERREDBY"=>"",
                        "LEADREFERREDBYID"=>"",
                        "LEADSTAGE"=> $activityStage,
                        "LEADSTATUS"=>"",
                        "LEADTYPE"=>"Affinity",
                        "LONGURL"=>$longUrl,
                        "LeadId"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "LemniskId"=>"",
                        "MATERNITYANDVACINATION"=>"",
                        "MIDDLENAME"=>!empty($empDetailsArray['emp_middlename']) ? $empDetailsArray['emp_middlename'] : "",
                        "MOBILE"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "NOMINEENAME"=>$nomineeName,
                        "NOMINEERELATION"=>$nomineeRelation,
                        "NOTESDESCRIPTION"=>"",
                        "NOTESTITLE"=>"",
                        "OPDEXPENSES"=>"",
                        "OneABCData"=>array(
                            "ABCinId"=>"",
                            "AddressLine3"=>"",
                            "Anniversary"=>"",
                            "CallBackDateTime"=>"",
                            "Category"=>"",
                            "Comments"=>"",
                            "CreatedByInSource"=>"",
                            "CustOKForCallOut"=>"",
                            "Description"=>"",
                            "GoldenId"=>"",
                            "Income"=>"",
                            "LOBPresence"=>"",
                            "LeadQualityCode"=>"",
                            "LeadSubSource"=>"ABC",
                            "RefDepartment"=>"",
                            "RefEmpEmail"=>"",
                            "RefEmpId"=>!empty($empDetailsArray['ref1']) ? $empDetailsArray['ref1'] : "",
                            "RefEmpName"=>"",
                            "RefEmpPhone"=>"",
                            "Remarks"=>"",
                            "SourceBusinessName"=>NULL,
                            "SourceFunctionName"=>NULL,
                            "SourceLeadId"=>"",
                            "SourceOwnerId"=>"",
                            "SourceOwnerName"=>"",
                            "SourceUserEmail"=>"",
                            "SourceUserId"=>"",
                            "SourceUserLOB"=>"",
                            "SourceUserLocation"=>"",
                            "SourceUserName"=>"",
                            "SpecificProductIntrest"=>"",
                            "SubSource"=>"",
                            "Telephone"=>""
                        ),
                        "PAN"=>!empty($empDetailsArray['pancard']) ? $empDetailsArray['pancard'] : "",
                        "PASSPORTNO"=>"",
                        "PAYMENTAMOUNT"=>$premium,
                        "PAYMENTSTATUS"=>$proposalStatus,
                        //"POLICYNO"=>!empty($proposalNumber) ? $proposalNumber : NULL,
                        "POLICYNO"=>$certificateNumber,
                        "PREEXISTINGDISEASEMEDICALCONDITION"=>"",
                        "PREFERREDCONTACTIBLETIME"=>NULL,
                        "PREFERREDMODEOFCONTACT"=>NULL,
                        "PREMIUM"=>$premium,
                        "PRODUCT"=>"Group Activ Health ",
                        "PROPOSALTYPE"=>!empty($activityType) && $activityType == 6 ? "STP" : "",
                        "QUOTENUMBER"=>"",
                        "REFERENCENUMBER"=>"",
                        "RESIDENTPHONE"=>"",
                        "ROOMTYPE"=>"",
                        "SALUTATION"=>!empty($empDetailsArray['salutation']) ? $empDetailsArray['salutation'] : "",
                        "SHORTURL"=>"",
                        "SOURCE"=>"ABC",
                        "SOURCEBUSINESSNAME"=>NULL,
                        "SOURCEFUNCTION"=>NULL,
                        "STATE"=>!empty($empDetailsArray['emp_state']) ? $empDetailsArray['emp_state'] : "",
                        "SUBSOURCE"=>"ABC",
                        "SUMINSURED"=>$sumInsured,
                        "Salary"=>!empty($empDetailsArray['annual_income']) ? $empDetailsArray['annual_income'] : "",
                        "TENURE"=>"",
                        "URL"=>"",
                        "UTMContent"=>"",
                        "VERIFIED"=>"",
                        "WEIGHT"=>"",
                        "ZONE"=>NULL,
                        "LEADSUBSOURCE"=>"Axis ABC"
                   );
        //print_pre($leadCreateArray);exit;               
        $jsonData = json_encode($leadCreateArray);
        
        $request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_LEAD_CRM_JSON", "product_id" => "ABC"];
        $this->db->insert("logs_docs",$request_arr);
        
        // Prepare new cURL resource
        $ch = curl_init('http://10.1.226.32/ABHICL_CRM/Service1.svc/LeadCreation');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
         
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
         
        // Submit the POST request
        $result = curl_exec($ch);
        
        if($result === false){
            $response =  'Curl error: ' . curl_error($ch);
        } else {
            $response = $result;
        }

       // print_pre($response);
        
        // Insert Data into Drop Off Log Start
        $this->db1->insert("drop_off_crm_data", [
        "type" => 'create_lead_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
        "requestresponse"=> $response
        ]);
        // Insert Data into Drop Off Log End
        
        return $response;
    }

    public function createCRMLeadDropOffabc($emp_id) {
        // Create LEAD CRM Start
        // $emp_id = '2602';
        $this->db1 = $this->load->database('axis_retail',TRUE);
        
        // Get Employee Details
        $sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
        $empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
        
        $customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",trim($empDetailsArray['customer_name'])) : "";
        
        // Get DOB
        $dateOfBirth = date("Y-m-d",strtotime($dob));
        $empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
        $empAge = 0;
        // DOB to Age Convertation Part Start
        if(!empty($empDOB)) {
            $today = date("Y-m-d");
            $diff = date_diff(date_create($empDOB), date_create($today));
            $empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;            
        }
        // DOB to Age Convertation Part End
        
        $policy_detail_id = $this->db->get_where("employee_product_details",array("emp_id" => $emp_id))->row_array();
        $policy_id = $policy_detail_id['policy_id'];
        // Customer JSON Data
        $jsonData = !empty($empDetailsArray['json_qote']) ? json_decode($empDetailsArray['json_qote'], true) : "";
        
        // Get Drop Off Long URL Start
        $sqlStr = "select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id
        FROM employee_details as ed INNER JOIN user_activity_abc as ua
        ON ed.emp_id = ua.emp_id AND ua.emp_id = $emp_id";
        //echo $sqlStr;exit;
        $aResult = $this->db1->query($sqlStr)->row_array();
        $activityType = $aResult['type'];
        
        // Activity Stage
        $activityStage = "";
        $rating = '';
        switch ($activityType) {
            case 1:
                $activityStage = "at Product Page";
                $rating = "Cold";
                break;
            case 2:
                $activityStage = "at know your premium page";
                $rating = "Cold";
                break;
            case 3:
                $activityStage = "at member enrollement page";
                $rating = "Warm";
                break;  
            case 4:
                $activityStage = "at review page";
                $rating = "Hot";
                break;  
            case 5:
                $activityStage = "at redirected to payment page";
                $rating = "Very Hot";
                break;  
            case 6:
                $activityStage = "at payment page";
                $rating = "Super-Hot";
                break; 
            case 7:
                $activityStage = "at thank you page";
                $rating = "Won";
                break;      
        }
        
    
        switch ($activityType) {
            case 1:
                $longUrl = base_url('comprehensive_product_abc');
                break;
            case 2:
                $longUrl = base_url('quotes_abc/'.$policy_id);
                break;
            case 3:
                $longUrl = base_url('member_proposer_detail');
                break;
            case 4:
                $longUrl = base_url('member_detail_product_abc');
                break;
             case ($activityType == 5 || $activityType == 6):
                $longUrl = base_url('payment_redirection');
                break;
            case 7:
                $longUrl = base_url('payment_success_view_call_abc/'.$emp_id);
                break;
        }
        // Get Drop Off Long URL End
        //echo $longUrl;exit;
        // Get Premium Start
        $query_premium = $this->db1->query("select SUM(epm.policy_mem_sum_premium) as policy_mem_sum_premium,epm.policy_mem_sum_insured,epm.familyConstruct from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$emp_id)->row_array();
        
        $premium = !empty($query_premium['policy_mem_sum_premium']) ? $query_premium['policy_mem_sum_premium'] : 0;
        $familyConstruct = !empty($query_premium['familyConstruct']) ? $query_premium['familyConstruct'] : 0;
        $sumInsured = !empty($query_premium['policy_mem_sum_insured']) ? $query_premium['policy_mem_sum_insured'] : 0;
        // Get Premium End
        
        // Get Proposal Details Start
        $queryProposal = "SELECT * FROM proposal WHERE emp_id = ".$emp_id;
        $query_proposal = $this->db1->query($queryProposal)->row_array();
        $proposalNumber = !empty($query_proposal['proposal_no']) ? $query_proposal['proposal_no'] : "";
        $proposalStatus = !empty($query_proposal['status']) ? $query_proposal['status'] : "";
        // Get Proposal Details End
        
        // Get Certificate Details Start
        $certificateNumber = NULL;
        if(!empty($proposalNumber)) {
            $queryCertificateDetails = "SELECT * FROM api_proposal_response WHERE proposal_no_lead = '".$proposalNumber."'";
            $query_CertificateDetails = $this->db1->query($queryCertificateDetails)->row_array();
            $certificateNumber = !empty($query_CertificateDetails['certificate_number']) ? $query_CertificateDetails['certificate_number'] : "";
        }
        // Get Certificate Details End
        
        // Get Nominee Details Start
        $queryNomineeDetails = "SELECT mpn.nominee_fname,mn.nominee_type FROM member_policy_nominee as mpn, master_nominee as mn WHERE mpn.fr_id = mn.nominee_id AND emp_id = ".$emp_id;
        $query_nominee = $this->db1->query($queryNomineeDetails)->row_array();
        $nomineeName = !empty($query_nominee['nominee_fname']) ? $query_nominee['nominee_fname'] : "";
        $nomineeRelation = !empty($query_nominee['nominee_type']) ? $query_nominee['nominee_type'] : "";
        // Get Nominee Details End
        
        // First Name, Last Name, Middle name
        $firstName = !empty($customerName[0]) ? $customerName[0] : "";
        $lastName = !empty($customerName) && count($customerName) > 2 ? $customerName[2] : $customerName[1];
        $middleName = "";
        $lastname = "";
        if(!empty($customerName) && count($customerName) > 2) {
            $middleName = !empty($customerName[1]) ? $customerName[1] : ".";
        } 
        
        $pinCode = !empty($empDetailsArray['emp_pincode']) ? $empDetailsArray['emp_pincode'] : "";
        
        // Create Lead API Array
        $leadCreateArray = array(
                        "AADHARNO"=>"",
                        "PINCODE"=>"",
                        "ACTIVITYDESCRIPTION"=>"",
                        "ACTIVITYSUBECT"=>"",
                        "ACTIVITYTYPE"=>NULL,
                        "ADDRESSLINE1"=>"",
                        "ADDRESSLINE2"=>"",
                        "ADDRESSLINE3"=>"", // NOT IN NEW
                        "AGE"=>$empAge,
                        "ALCOHOLTABBACOCONSUMPTION"=>"",
                        "APPLICATIONNO"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "PartnerLeadId"=>"", // NOT IN NEW
                        "ATTACHMENTS"=>array(),
                        "Adgroup"=>"",
                        "AffiliateDiscountFlag"=>"",
                        "CITY"=>!empty($empDetailsArray['emp_city']) ? $empDetailsArray['emp_city'] : "",
                        "CONTACTNUMBER"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "COPAYMENTWAIVER"=>"",
                        "COVER"=>"Self",
                        "CUSTOMERTYPE"=>"",
                        // "DATEOFBIRTH"=>$empDOB,
                        "DATEOFBIRTH"=>date("d/m/Y", strtotime($empDOB)),
                        "DEDUCTIBLES"=>"",
                        "DRIVINGLICENSENO"=>"",
                        "EMAIL"=>!empty($empDetailsArray['email']) ? $empDetailsArray['email'] : "",
                        "EXISTINGINSURANCE"=>"",
                        "EXISTINGINSURANCECOVER"=>"",
                        "EXISTINGINSURER"=>"",
                        "EmployeeDiscountFlag"=>"",
                        "EmployeeID"=>"",
                        "FAMILYCONSTRUCT"=>"", // NOT IN NEW
                        "FIRSTNAME"=>$firstName,
                        "MIDDLENAME" => $middleName,
                        "LASTNAME"=>$lastName,                      
                        "GCLID"=>"",
                        "GENDER"=>!empty($empDetailsArray['gender']) ? $empDetailsArray['gender'] : "",
                        "HEIGHT"=>"",
                        "HOSPITALCASHBENEFIT"=>"",
                        "AutoRenewalFlag"=>"", // NOT IN NEW
                        "Rating"=> "", // NOT IN NEW
                        "IDNUMBER"=>"",
                        "IDTYPE"=>NULL,
                        "INTERMEDIARYCODE"=>"",
                        "Keyword"=>"",
                        "LEADCREATEDBY"=>"",
                        "LEADOWNER"=>"",
                        "LEADREFERREDBY"=>"",
                        "LEADREFERREDBYID"=>"",
                        "LEADSTAGE"=> "",
                        "LEADSTATUS"=>"",
                        "LEADTYPE"=>"",
                        "LONGURL"=>"",
                        "LeadId"=>"",
                        "LemniskId"=>"",
                        "MATERNITYANDVACINATION"=>"",
                        "MIDDLENAME"=>"",
                        "MOBILE"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "NOMINEENAME"=>"",
                        "NOMINEERELATION"=>"",
                        "NOTESDESCRIPTION"=>"",
                        "NOTESTITLE"=>"",
                        "OPDEXPENSES"=>"",
                        "OneABCData"=>array(
                            "ABCinId"=>"",
                            "AddressLine3"=>"",
                            "Anniversary"=>"",
                            "CallBackDateTime"=>"",
                            "Category"=>"",
                            "Comments"=>"",
                            "CreatedByInSource"=>"ABCPortal",
                            "CustOKForCallOut"=>"",
                            "Description"=>"",
                            "GoldenId"=>"",
                            "Income"=>"",
                            "LOBPresence"=>"",
                            "LeadQualityCode"=>"",
                            "LeadSubSource"=>"",
                            "RefDepartment"=>"",
                            "RefEmpEmail"=>"",
                            "RefEmpId"=>"",
                            "RefEmpName"=>"",
                            "RefEmpPhone"=>"",
                            "Remarks"=>"",
                            "SourceBusinessName"=>NULL,
                            "SourceFunctionName"=>NULL,
                            "SourceLeadId"=>"",
                            "SourceOwnerId"=>"",
                            "SourceOwnerName"=>"",
                            "SourceUserEmail"=>"",
                            "SourceUserId"=>"",
                            "SourceUserLOB"=>"",
                            "SourceUserLocation"=>"",
                            "SourceUserName"=>"",
                            "SpecificProductIntrest"=>"",
                            "SubSource"=>"",
                            "Telephone"=>""
                        ),
                        "PAN"=>"",
                        "PASSPORTNO"=>"",
                        "PAYMENTAMOUNT"=>"",
                        "PAYMENTSTATUS"=>"",
                        //"POLICYNO"=>!empty($proposalNumber) ? $proposalNumber : NULL,
                        "POLICYNO"=>"",
                        "PREEXISTINGDISEASEMEDICALCONDITION"=>"",
                        "PREFERREDCONTACTIBLETIME"=>"",
                        "PREFERREDMODEOFCONTACT"=>"",
                        "PREMIUM"=>$premium,
                        "PRODUCT"=>"Sampoorna",
                        // "PROPOSALTYPE"=>"",
                        "PROPOSALTYPE"=> "NSTP",
                        "QUOTENUMBER"=>"",
                        "REFERENCENUMBER"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "RESIDENTPHONE"=>"",
                        "ROOMTYPE"=>"",
                        "SALUTATION"=>"",
                        "SHORTURL"=>"",
                        "SOURCE"=>"ABC_Xsell_1cr",
                        "SOURCEBUSINESSNAME"=>NULL,
                        "SOURCEFUNCTION"=>NULL,
                        "STATE"=>"",
                        "SUBSOURCE"=>"ABC_Xsell_1cr",
                        "SUMINSURED"=>"",
                        "Salary"=>"",
                        "TENURE"=>"",
                        "URL"=>$longUrl,
                        "UTMContent"=>"",
                        "VERIFIED"=>"",
                        "WEIGHT"=>"",
                        "ZONE"=>NULL,
                        "LEADSUBSOURCE"=>""
                   );
        //print_pre($leadCreateArray);exit;               
        $jsonData = json_encode($leadCreateArray);
        
        $request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_LEAD_CRM_JSON", "product_id" => "ABC"];
        $this->db->insert("logs_docs",$request_arr);
        
        // Prepare new cURL resource
        $ch = curl_init('http://10.1.226.39:8080/LeadAPI/Service1.svc/Lead/CreateLead');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
         
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
         
        // Submit the POST request
        $result = curl_exec($ch);
        
        if($result === false){
            $response =  'Curl error: ' . curl_error($ch);
        } else {
            $response = $result;
        }

       // print_pre($response);
        
        // Insert Data into Drop Off Log Start
        $this->db1->insert("drop_off_crm_data", [
        "type" => 'create_lead_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
        "requestresponse"=> $response
        ]);
        // Insert Data into Drop Off Log End
        
        return $response;
    }
	}

