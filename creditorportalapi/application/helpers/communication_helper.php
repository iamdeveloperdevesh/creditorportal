<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function triggerCommunication(array $alert_id, array $data){

    $CI = &get_instance();

    if(empty($alert_id) || empty($data)){

        $response = ["status_code" => 500, "msg" => "Alert ID and data are required"];

        $CI->db->insert("application_logs", [
            "lead_id" => $data['lead_id'],
            "action" => "communication_sent",
            "request_data" => '',
            "response_data" => json_encode($response),
            "created_on" => date("Y-m-d H:i:s")
        ]);

        return $response;
    }

    $query = $CI->db->query("SELECT `alert_id`, `alert_mode` FROM `comm_triggers` WHERE `alert_id` IN (\"".implode('","', $alert_id)."\")");

    if ($query->num_rows() > 0) {

		$alert_modes = $query->result_array();
        
        foreach($alert_modes as $alert_mode){

            if(isset($alert_mode['alert_mode']) && $alert_mode['alert_mode']){

                $request = [
                    "RTdetails" => [
                        "PolicyID" => "",
                        "AppNo" => "201901230001211",
                        "alertID" => $alert_mode['alert_id'],
                        "channel_ID" => "ABHI Creditor Portal",
                        "Req_Id" => "1",
                        "field1" => "",
                        "field2" => "",
                        "field3" => "",
                        "Alert_Mode" => $alert_mode['alert_mode'],
                        "Alertdata" => [
                            "mobileno" => isset($data['mobile_no']) ? substr(trim($data['mobile_no']), -10) : "",
                            "emailId" => isset($data['email_id']) ? $data['email_id'] : "",
                        ]
                    ]
                ];

                foreach($data['alerts'] as $key => $value){

                    if(filter_var($value, FILTER_VALIDATE_URL)){

                        if(strlen($value) >= 30){

                            $request["RTdetails"]["Alert_Mode"] = 2;
                        }
                    }
                    else{

                        if(strlen($value) >= 30){

                            $value = substr($value, 0, 29);
                        }
                    }

                    $request["RTdetails"]["Alertdata"]["AlertV".($key + 1)] = $value;
                }

                $CI->load->model('commonapi/commonapimodel');

                $doCommunicate = $CI->commonapimodel->doCommunicate($request);

                $request_arr = [
                    "lead_id" => $data['lead_id'],
                    "req" => json_encode($request),
                    "res" => json_encode($doCommunicate),
                    "product_id" => $data['plan_id'],
                    "type" => "communication_sent",
                ];

                $CI->db->insert("logs_docs", $request_arr);

                //Application log entries
                $CI->db->insert("application_logs", [
                    "lead_id" => $data['lead_id'],
                    "action" => "communication_sent",
                    "request_data" => json_encode($request),
                    "response_data" => json_encode($doCommunicate),
                    "created_on" => date("Y-m-d H:i:s")
                ]);

                return [
                    "status_code" => "200",
                    "data" => $doCommunicate
                ];
            }
            else{

                $response = ["status_code" => 500, "msg" => "Alert Mode does not exist"];

                $CI->db->insert("application_logs", [
                    "lead_id" => $data['lead_id'],
                    "action" => "communication_sent",
                    "request_data" => json_encode(['alert_id' => $alert_mode['alert_id']]),
                    "response_data" => json_encode($response),
                    "created_on" => date("Y-m-d H:i:s")
                ]);

                return $response;
            }
        }
	}
    else{

        $response = ["status_code" => 500, "msg" => "Alert ID does not exist"];

        $CI->db->insert("application_logs", [
            "lead_id" => $data['lead_id'],
            "action" => "communication_sent",
            "request_data" => json_encode($alert_id),
            "response_data" => json_encode($response),
            "created_on" => date("Y-m-d H:i:s")
        ]);

        return $response;
    }
}