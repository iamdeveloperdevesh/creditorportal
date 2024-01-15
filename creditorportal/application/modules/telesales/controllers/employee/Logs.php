<?php

ini_set('max_execution_time', 0);
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Logs extends CI_Controller {

    function __construct() {
        parent::__construct();
        

        $this->db2 = $this->load->database('axis_retail', TRUE);
        
        // Added By Shardul
        $this->load->model('cron/cron_m');
        //$this->output->enable_profiler(TRUE);
        
//        if ($this->session->userdata('login_data') == 'logins') {
//            $this->emp_id = $this->session->userdata('emp_id_admin');
//        } else {
//            $this->emp_id = $this->session->userdata('emp_id');
//        }
//        if (!$this->emp_id) {
//            redirect('login');
//        }

        if ($_SESSION['is_redirect_allow'] != "1")
        {
            redirect('login');
        }
    }

    function index() {
        //echo 1;print_pre($this->input->post());
        $data['result'] = [];
        $this->load->employee_template("log_view", $data);
    }

    function get_mis_report() {
        extract($this->input->post(null, true));

        $data = $this->db->query("SELECT ed.email,ed.lead_id,ed.created_at,ed.emp_firstname,ed.emp_lastname,p.premium,p.sum_insured,pm.familyConstruct,pd.payment_status,pd.txndate,p.branch_code AS branch_sol_id,p.IMDCode,apr.certificate_number,acr.COI_No,e.product_name,p.proposal_no,epd.policy_sub_type_id
        FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
        family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
        left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
        left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
        where epd.product_name = e.id
        AND pd.proposal_id = p.id
        AND epd.policy_detail_id = p.policy_detail_id
        AND p.id = pm.proposal_id
        AND fr.family_id = 0
        AND pm.family_relation_id = fr.family_relation_id
        AND fr.emp_id = ed.emp_id group by p.id
        UNION ALL
        SELECT ed.email,ed.lead_id,ed.created_at,ed.emp_firstname,ed.emp_lastname,'NULL' AS 'premium','NULL' AS 'sum_insured','NULL' AS 'familyConstruct','NULL' AS 'payment_status','NULL' AS 'txndate','NULL' AS 'branch_sol_id','NULL' AS 'IMDCode','NULL' AS 'certificate_number','NULL' AS 'COI_No',m.product_name,'NULL' AS 'proposal_no','NULL' AS 'policy_sub_type_id' FROM employee_details AS ed, family_relation AS fr,product_master_with_subtype AS m
        WHERE ed.emp_id = fr.emp_id
        AND m.product_code = ed.product_id
        AND fr.family_id = 0
        AND ed.emp_id NOT IN(SELECT ed1.emp_id FROM proposal AS p,employee_details AS ed1
        WHERE p.emp_id = ed1.emp_id)
        GROUP BY ed.emp_id")->result_array();

        $agentData = [$data];
        $this->load->library("excel");
        $config = [
            'filename' => 'Certification_' . date("d-m-Y"), // prove any custom name here
            'use_sheet_name_as_key' => false, // this will consider every first index from an associative array as main headings to the table
            'use_first_index' => true, // if true then it will set every key as sheet name for appropriate sheet
        ];
        $sheetdata = Excel::export($agentData, $config);
    }

    function getlogs($i) {
        extract($this->input->post(null, true));

        if ($i == 1) {
            $page = 0;
        } else {
            $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        }



        // 2019124915894765
        $data['result'] = [];

        if ($_POST['product_type'] == 'R03' && isset($_POST['product_type'])) {
            if (($_POST['lead_id'])) {

                $data['result'] = $this->db->query('SELECT count(ed.lead_id) as lead_id
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND ed.lead_id ="' . $lead_id . '"
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id
            AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by ed.lead_id')->result();
                $config["total_rows"] = $data['result'][0]->lead_id;
                $data['result'] = $this->db->query('SELECT ed.lead_id,ed.created_at,p.proposal_no,ed.emp_firstname,ed.emp_lastname,p.premium,p.sum_insured,pm.familyConstruct,p.status as payment_status,pd.txndate,p.IMDCode,apr.certificate_number,acr.COI_No,e.policy_subtype_id, acr.status as acrs, apr.status as aprs
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND ed.lead_id ="' . $lead_id . '"
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id
            AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by p.id LIMIT 10 OFFSET ' . $page)->result();
            } else {
                $arr = explode('-', $this->input->post('dates'));
                $start_date = str_replace('/', '-', $arr[0]);
                $end_date = str_replace('/', '-', $arr[1]);

                if ($this->input->post('time_to')) {

                    $start_date = $start_date . ' ' . $this->input->post('time_to') . ':0:0';
                } else {
                    $start_date = $start_date . ' ' . '0:0:0';
                }
                if ($this->input->post('time_from')) {
                    $end_date = $end_date . ' ' . $this->input->post('time_from') . ':59:59';
                } else {
                    $end_date = $end_date . ' ' . '23:59:59';
                }


                //echo $start_date; echo $end_date;exit;
                $query = 'select count(lead_id) as lead_id from (SELECT count(ed.lead_id) as lead_id
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND p.created_date BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id';

                if ($proposal_status) {
                    $query .= ' AND p.status = "' . $proposal_status . '"';
                }
                $query .= ' AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by p.id ) e';

                $data['result'] = $this->db->query($query)->result();
                //echo $this->db->last_query();
                $config["total_rows"] = $data['result'][0]->lead_id;
                $query = 'SELECT ed.lead_id,ed.created_at,p.proposal_no,ed.emp_firstname,ed.emp_lastname,p.premium,p.sum_insured,pm.familyConstruct,p.status as payment_status,pd.txndate,p.IMDCode,apr.certificate_number,acr.COI_No,e.policy_subtype_id, acr.status as acrs, apr.status as aprs
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND p.created_date BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
            
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id';
                if ($proposal_status) {
                    $query .= ' AND p.status = "' . $proposal_status . '"';
                }

                $query .= ' AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by p.id LIMIT 10 OFFSET ' . $page;
                $data['result'] = $this->db->query($query)->result();
                //echo $this->db->last_query();exit;
            }
        } else {
            //product type R05

            if ($_POST['product_type'] == 'R05' && isset($_POST['product_type'])) {
                if (($_POST['lead_id'])) {

                    $data['result'] = $this->db2->query('SELECT count(ed.lead_id) as lead_id
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND ed.lead_id ="' . $lead_id . '"
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id
            AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by ed.lead_id')->result();
                    //echo $this->db2->last_query();




                    $config["total_rows"] = $data['result'][0]->lead_id;
                    $data['result'] = $this->db2->query('SELECT ed.lead_id,ed.created_at,p.proposal_no,ed.customer_name as emp_firstname,p.premium,p.sum_insured,pm.familyConstruct,p.status as payment_status,pd.txndate,p.IMDCode,apr.certificate_number,acr.COI_No,e.policy_subtype_id, acr.status as acrs, apr.status as aprs
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND ed.lead_id ="' . $lead_id . '"
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id
            AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by p.id LIMIT 10 OFFSET ' . $page)->result();
                    //echo $this->db2->last_query();
                } else {

                    $arr = explode('-', $this->input->post('dates'));
                    $start_date = str_replace('/', '-', $arr[0]);
                    $end_date = str_replace('/', '-', $arr[1]);


                    if ($this->input->post('time_to')) {

                        $start_date = $start_date . ' ' . $this->input->post('time_to') . ':0:0';
                    } else {
                        $start_date = $start_date . ' ' . '0:0:0';
                    }
                    if ($this->input->post('time_from')) {
                        $end_date = $end_date . ' ' . $this->input->post('time_from') . ':59:59';
                    } else {
                        $end_date = $end_date . ' ' . '23:59:59';
                    }
                    //echo $start_date;
                    //AND p.status = "'.$proposal_status.'" 
                    $query = ' select count(lead_id) as lead_id from (SELECT count(ed.lead_id) as lead_id
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND p.created_date BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
            
            AND pd.proposal_id = p.id
            AND epd.policy_detail_id = p.policy_detail_id';
                    if ($proposal_status) {
                        $query .= ' AND p.status = "' . $proposal_status . '"';
                    }
                    $query .= ' AND p.id = pm.proposal_id
            AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by p.id ) e';
                    $data['result'] = $this->db2->query($query)->result();
                    $config["total_rows"] = $data['result'][0]->lead_id;
                    //echo $this->db2->last_query();
                    //print_pre($data['result']);
                    //echo 'sdsdsds' ;echo $data['result'][0]->lead_id;
                    //exit;

                    $query = 'SELECT ed.lead_id,ed.created_at,p.proposal_no,ed.customer_name as emp_firstname,p.premium,p.sum_insured,pm.familyConstruct,p.status as payment_status,pd.txndate,p.IMDCode,apr.certificate_number,acr.COI_No,e.policy_subtype_id, acr.status as acrs, apr.status as aprs
            FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS pm,
            family_relation AS fr, employee_details AS ed,payment_details as pd,proposal AS p
            left join api_proposal_response as apr on apr.proposal_no_lead = p.proposal_no
            left join api_cancer_response as acr on p.proposal_no = acr.Proposal_id
            where epd.product_name = e.id
            AND p.created_date BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
            
            AND pd.proposal_id = p.id';
                    if ($proposal_status) {
                        $query .= ' AND p.status = "' . $proposal_status . '"';
                    }
                    $query .= ' AND epd.policy_detail_id = p.policy_detail_id
            AND p.id = pm.proposal_id
            AND fr.family_id = 0
            AND pm.family_relation_id = fr.family_relation_id
            AND fr.emp_id = ed.emp_id  group by p.id LIMIT 10 OFFSET ' . $page;
                    $data['result'] = $this->db2->query($query)->result();

                    //print_pre($this->db2->last_query());exit;
                }
            }
        }

        //print_pre($this->db->last_query());exit;
        $this->load->library("pagination");
        //$config["total_rows"] = 20;
        $config["per_page"] = 10;
        $config["num_links"] = 5;
        $config["base_url"] = base_url() . "post_log";





        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();

        if ($_POST['product_type'] == 'R03') {
            $data["query"] = $this->db->last_query();
        } else {
            $data["query"] = $this->db2->last_query();
        }

        //print_pre($data);exit;
        $this->load->employee_template("log_view", $data);
    }

    function all_logs_download() {


        $data['result'] = [];





        if ($_POST['product_type'] == 'R03' && isset($_POST['product_type'])) {
            if ($_POST['lead_id'] == '') {
                if ($_POST['certificate_no'] && $_POST['certificate_no'] != '') {
                    $ghi_cert = $this->db->select('ed.lead_id')
                            ->from('api_cancer_response acr')
                            ->join('employee_details ed', 'acr.emp_id = ed.emp_id')
                            ->where('acr.COI_No', $_POST['certificate_no'])
                            ->get()
                            ->row_array();
                    if (count($ghi_cert) > 0) {
                        $_POST['lead_id'] = $ghi_cert['lead_id'];
                    } else {
                        $ghi_cert = $this->db->select('ed.lead_id')
                                ->from('api_proposal_response apr')
                                ->join('employee_details ed', 'apr.emp_id = ed.emp_id')
                                ->where('apr.certificate_number', $_POST['certificate_no'])
                                ->get()
                                ->row_array();
                        if (count($ghi_cert) > 0) {
                            $_POST['lead_id'] = $ghi_cert['lead_id'];
                        } else {
                            
                        }
                    }
                }
            }
            //print_pre($this->input->post());exit;
            //Axis_redirection_post_data_request,Axis_redirection_post_data_decrypted
            //list full_quote_request1,payment_request_post,full_quote_request2,cancer_request
            //full_quote_request1_posH01,full_quote_request2_posH01,full_quote_request1_posH02,
            //full_quote_request2_posH02, sms_logs_redirect

            if (($_POST['lead_id'])) {
                $lead_id = $_POST['lead_id'];
                if ($_POST['filter_type_ro3'] != '') {

                    $data['result'] = $this->db->query('select * from logs_docs where lead_id ="' . $lead_id . '" AND type = "' . $_POST['filter_type_ro3'] . '" ORDER BY created_at desc')->result_array();
                    //echo $this->db->last_query();exit;
                } else {


                    $data['result'] = $this->db->query('select * from logs_docs where lead_id ="' . $lead_id . '" ORDER BY created_at desc')->result_array();
                    echo $this->db->last_query();exit;
                }
            } else {
                ////Axis_redirection_post_data_request,Axis_redirection_post_data_decrypted //full_quote_request1_retail_payment,full_quote_request2_retail_payment,payment_request_post,coi_genarate,coi_uid_genarate,
                //sms_logs,payment_response_post,bitly_url,payu_real_check



                $lead_id = $_POST['lead_id'];
                $arr = explode('-', $this->input->post('dates'));
                $start_date = str_replace('/', '-', $arr[0]);
                $end_date = str_replace('/', '-', $arr[1]);

                if ($this->input->post('time_to')) {

                    $start_date = $start_date . ' ' . $this->input->post('time_to') . ':0:0';
                } else {
                    $start_date = $start_date . ' ' . '0:0:0';
                }
                if ($this->input->post('time_from')) {
                    $end_date = $end_date . ' ' . $this->input->post('time_from') . ':59:59';
                } else {
                    $end_date = $end_date . ' ' . '23:59:59';
                }
                if ($_POST['filter_type_ro3'] != '') {

                    //echo $this->db->last_query();
                    $data['result'] = $this->db->query('select * from logs_docs where  (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ) AND type = "' . $_POST['filter_type_ro3'] . '" ORDER BY created_at desc')->result_array();
//echo $this->db->last_query();exit;
                } else {


                    $data['result'] = $this->db->query('select * from logs_docs where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc')->result_array();
//echo $this->db->last_query();exit;
                }
            }
        } else {
            //product type R05

            if ($_POST['product_type'] == 'R05' && isset($_POST['product_type'])) {
                if ($_POST['lead_id'] == '') {
                    if ($_POST['certificate_no'] && $_POST['certificate_no'] != '') {
                        $ghi_cert = $this->db2->select('ed.lead_id')
                                ->from('api_proposal_response apr')
                                ->join('employee_details ed', 'apr.emp_id = ed.emp_id')
                                ->where('apr.certificate_number', $_POST['certificate_no'])
                                ->get()
                                ->row_array();
                        if (count($ghi_cert) > 0) {
                            $_POST['lead_id'] = $ghi_cert['lead_id'];
                        } else {
                            
                        }
                    }
                }
                if (($_POST['lead_id'])) {
                    $lead_id = $_POST['lead_id'];
                    if ($_POST['filter_type_ro5'] != '') {
                        // $data['count'] = $this->db2->query(' select  sum(lead_id)  as lead_id from (  select count("lead_id") as lead_id from logs_post_data where lead_id ="'.$lead_id.'" and type = "'.$_POST['filter_type_ro5'].'" UNION ALL select count("lead_id") as lead_id from logs_docs where lead_id ="'.$lead_id.'" and type = "'.$_POST['filter_type_ro5'].'") e')->result();

                        $logs_post_data_table = ['Axis_redirection_post_data_request', 'Axis_redirection_post_data_decrypted'];
                        if (in_array($_POST['filter_type_ro5'], $logs_post_data_table)) {
                            $table = 'logs_post_data';
                        } else {
                            $table = 'logs_docs';
                        }


                        $data['result'] = $this->db2->query('select req,res,lead_id,type from ' . $table . ' where lead_id ="' . $lead_id . '" AND type = "' . $_POST['filter_type_ro5'] . '" ORDER BY created_at desc')->result_array();
                        //echo $this->db2->last_query();exit;
                    } else {
                        //without filter



                        /*$data['result'] = $this->db2->query('(select req,res,lead_id,type from logs_post_data where lead_id ="' . $lead_id . '" ORDER BY created_at desc ) UNION ALL (select req,res,lead_id,type from logs_docs where lead_id ="' . $lead_id . '" ORDER BY created_at desc )')->result_array();*/
                        
                        $strSql = 'SELECT combine.*
                                      FROM ( SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               WHERE lead_id ="'.$lead_id .'"
                                               
                                              UNION
                                              SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                WHERE lead_id ="'.$lead_id .'"
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC';
                                //echo $strSql;die;  
                        $rsResult = $this->db2->query($strSql);
                        $data['result'] =  $rsResult->result();
                        
                    }
                } else {
                    $lead_id = $_POST['lead_id'];
                    $arr = explode('-', $this->input->post('dates'));
                    $start_date = str_replace('/', '-', $arr[0]);
                    $end_date = str_replace('/', '-', $arr[1]);

                    if ($this->input->post('time_to')) {

                        $start_date = $start_date . ' ' . $this->input->post('time_to') . ':0:0';
                    } else {
                        $start_date = $start_date . ' ' . '0:0:0';
                    }
                    if ($this->input->post('time_from')) {
                        $end_date = $end_date . ' ' . $this->input->post('time_from') . ':59:59';
                    } else {
                        $end_date = $end_date . ' ' . '23:59:59';
                    }

                    if ($_POST['filter_type_ro5'] != '') {
                        $logs_post_data_table = ['Axis_redirection_post_data_request', 'Axis_redirection_post_data_decrypted'];
                        if (in_array($_POST['filter_type_ro5'], $logs_post_data_table)) {
                            $table = 'logs_post_data';
                        } else {
                            $table = 'logs_docs';
                        }


                        $data['result'] = $this->db2->query('select req,res,lead_id,type from ' . $table . ' where  (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ) AND type = "' . $_POST['filter_type_ro5'] . '" ORDER BY created_at desc')->result_array();
//echo $this->db2->last_query();exit;
                    } else {


                        /*$data['result'] = $this->db2->query('(select req,res,lead_id,type from logs_post_data where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc )UNION ALL (select req,res,lead_id,type from logs_docs where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc)')->result_array();*/
                        
                        $strSql = 'SELECT combine.*
                                      FROM ( SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               WHERE created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '"
                                               AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
                                              UNION
                                              SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                WHERE created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" 
                                                AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                     
                        $rsResult = $this->db2->query($strSql);
                        $data['result'] =  $rsResult->result();
                        
                        //echo $this->db2->last_query();
                        //echo 'ss'.$config["total_rows"];exit;
                    }
                    //echo $start_date;
                    //echo $this->db2->last_query();exit;
                }
            }
        }

        // if($_POST['product_type'] == 'R03'){
        // echo $this->db->last_query();
        // }else{
        // echo  $this->db2->last_query();
        // }
        //print_pre($data);
        if (!empty($data['result'])) {
            $Proposal = [$data['result']];
        } else {
            $Proposal = [[['lead_id' => '', 'type' => '', 'req' => '', 'res' => '']]];
        }


        $this->load->library("excel");
        $config = [
            'filename' => 'LOGS' . date("d-m-Y"), // prove any custom name here
            'use_sheet_name_as_key' => false, // this will consider every first index from an associative array as main headings to the table
            'use_first_index' => true, // if true then it will set every key as sheet name for appropriate sheet
        ];
        $sheetdata = Excel::export($Proposal, $config);
    }

    function all_logs_OLD($i) {
        //print_pre($this->input->post());
        if ($i == 1) {
            $page = 0;
        } else {
            $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        }
        $data['result'] = [];
        if ($_POST['product_type'] == 'R03' && isset($_POST['product_type'])) {
            if ($_POST['lead_id'] == '') {
                if ($_POST['certificate_no'] && $_POST['certificate_no'] != '') {
                    $ghi_cert = $this->db->select('ed.lead_id')
                            ->from('api_cancer_response acr')
                            ->join('employee_details ed', 'acr.emp_id = ed.emp_id')
                            ->where('acr.COI_No', $_POST['certificate_no'])
                            ->get()
                            ->row_array();
                    if (count($ghi_cert) > 0) {
                        $_POST['lead_id'] = $ghi_cert['lead_id'];
                    } else {
                        $ghi_cert = $this->db->select('ed.lead_id')
                                ->from('api_proposal_response apr')
                                ->join('employee_details ed', 'apr.emp_id = ed.emp_id')
                                ->where('apr.certificate_number', $_POST['certificate_no'])
                                ->get()
                                ->row_array();
                        if (count($ghi_cert) > 0) {
                            $_POST['lead_id'] = $ghi_cert['lead_id'];
                        } else {
                            $this->load->employee_template("log_all_view", $data);
                            return;
                        }
                    }
                }
            }
            //print_pre($this->input->post());exit;
            //Axis_redirection_post_data_request,Axis_redirection_post_data_decrypted
            //list full_quote_request1,payment_request_post,full_quote_request2,cancer_request
            //full_quote_request1_posH01,full_quote_request2_posH01,full_quote_request1_posH02,
            //full_quote_request2_posH02, sms_logs_redirect

            if (($_POST['lead_id'])) {
                $lead_id = $_POST['lead_id'];
                if ($_POST['filter_type_ro3'] != '') {
                    $data1['count'] = $this->db->query('select count("lead_id") as lead_id from logs_docs where lead_id ="' . $lead_id . '" and type = "' . $_POST['filter_type_ro3'] . '"')->result();
                    $data2['count'] = $this->db->query('select count("lead_id") as lead_id from logs_post_data where lead_id ="' . $lead_id . '" and type = "' . $_POST['filter_type_ro3'] . '"')->result();
                    
                    //start
                        if ($data1['count'][0]->lead_id > $data2['count'][0]->lead_id) {
                            $config["total_rows"] = $data1['count'][0]->lead_id;
                        } else {
                            $config["total_rows"] = $data2['count'][0]->lead_id;
                        }
                        
                        /*$data['result'] = $this->db2->query('(select req,res,lead_id,created_at,type from logs_post_data where lead_id ="' . $lead_id . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page . ') UNION ALL (select req,res,lead_id,created_at,type from logs_docs where lead_id ="' . $lead_id . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page . ')')->result();*/
                        
                        $strSql = 'SELECT combine.*
                                      FROM ( SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               WHERE lead_id ="'.$lead_id .'" AND type = "' . $_POST['filter_type_ro3'] . '"
                                               
                                              UNION
                                              SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                WHERE lead_id ="'.$lead_id .'" AND type = "' . $_POST['filter_type_ro3'] . '"
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                //echo $strSql;die;  
                        $rsResult = $this->db->query($strSql);
                        $data['result'] =  $rsResult->result();
                        //echo $this->db->last_query();exit;
                } else {
                    $data1['count'] = $this->db->query('select count("lead_id") as lead_id from logs_docs where lead_id ="' . $lead_id . '"')->result();
                       $data2['count'] = $this->db->query('select count("lead_id") as lead_id from logs_post_data where lead_id ="' . $lead_id . '"')->result();
                         if ($data1['count'][0]->lead_id > $data2['count'][0]->lead_id) {
                            $config["total_rows"] = $data1['count'][0]->lead_id;
                        } else {
                            $config["total_rows"] = $data2['count'][0]->lead_id;
                        }
                   
                    $strSql = 'SELECT combine.*
                                      FROM ( (SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               WHERE lead_id ="'.$lead_id .'" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page.')
                                               
                                              UNION
                                              (SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                WHERE lead_id ="'.$lead_id .'"  ORDER BY created_at desc LIMIT 10 OFFSET ' . $page.')
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                //echo $strSql;die;  
                        $rsResult = $this->db->query($strSql);
                        $data['result'] =  $rsResult->result();
                  //  echo $this->db->last_query();exit;
                }

                $config["total_rows"] = $data['count'][0]->lead_id;
            } else {
                ////Axis_redirection_post_data_request,Axis_redirection_post_data_decrypted //full_quote_request1_retail_payment,full_quote_request2_retail_payment,payment_request_post,coi_genarate,coi_uid_genarate,
                //sms_logs,payment_response_post,bitly_url,payu_real_check
                $lead_id = $_POST['lead_id'];
                $arr = explode('-', $this->input->post('dates'));
                $start_date = str_replace('/', '-', $arr[0]);
                $end_date = str_replace('/', '-', $arr[1]);

                if ($this->input->post('time_to')) {

                    $start_date = $start_date . ' ' . $this->input->post('time_to') . ':0:0';
                } else {
                    $start_date = $start_date . ' ' . '0:0:0';
                }
                if ($this->input->post('time_from')) {
                    $end_date = $end_date . ' ' . $this->input->post('time_from') . ':59:59';
                } else {
                    $end_date = $end_date . ' ' . '23:59:59';
                }
                if ($_POST['filter_type_ro3'] != '') {
                    $data1['count'] = $this->db->query('select count(lead_id) as lead_id from logs_docs where  (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '") AND type ="' . $_POST['filter_type_ro3'] . '"')->result();
                    $data2['count'] = $this->db->query('select count(lead_id) as lead_id from logs_post_data where  (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '") AND type ="' . $_POST['filter_type_ro3'] . '"')->result();
                    if ($data1['count'][0]->lead_id > $data2['count'][0]->lead_id) {
                            $config["total_rows"] = $data1['count'][0]->lead_id;
                        } else {
                            $config["total_rows"] = $data2['count'][0]->lead_id;
                        }
                    
                    
                    $strSql = 'SELECT combine.*
                                      FROM ( (SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data where
                                               (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ) AND type = "' . $_POST['filter_type_ro3'] . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page.')
                                               
                                              UNION
                                              (SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs where 
                                                (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ) AND type = "' . $_POST['filter_type_ro3'] . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page.')
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                //echo $strSql;die;  
                                
                        $rsResult = $this->db->query($strSql);
                        $data['result'] =  $rsResult->result();
                        //echo $this->db->last_query();exit;
                    
                } else {
                    $data1['count'] = $this->db->query('select count(lead_id) as lead_id from logs_docs where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"')->result();
                    $data2['count'] = $this->db->query('select count(lead_id) as lead_id from logs_post_data where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"')->result();
                    
                    if ($data1['count'][0]->lead_id > $data2['count'][0]->lead_id) {
                            $config["total_rows"] = $data1['count'][0]->lead_id;
                        } else {
                            $config["total_rows"] = $data2['count'][0]->lead_id;
                        }
                    $strSql = 'SELECT combine.*
                                      FROM ( (SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page .')
                                               
                                              UNION
                                              (SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page .')
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                //echo $strSql;die;  
                        $rsResult = $this->db->query($strSql);
                        $data['result'] =  $rsResult->result();
                    
                    //echo $this->db->last_query();exit;
                }

                $config["total_rows"] = $data['count'][0]->lead_id;
                //echo $this->db->last_query();
            }
        } else {
            //product type R05

            if ($_POST['product_type'] == 'R05' && isset($_POST['product_type'])) {
                if ($_POST['lead_id'] == '') {
                    if ($_POST['certificate_no'] && $_POST['certificate_no'] != '') {
                        $ghi_cert = $this->db2->select('ed.lead_id')
                                ->from('api_proposal_response apr')
                                ->join('employee_details ed', 'apr.emp_id = ed.emp_id')
                                ->where('apr.certificate_number', $_POST['certificate_no'])
                                ->get()
                                ->row_array();
                        if (count($ghi_cert) > 0) {
                            $_POST['lead_id'] = $ghi_cert['lead_id'];
                        } else {
                            $this->load->employee_template("log_all_view", $data);
                            return;
                        }
                    }
                }
                if (($_POST['lead_id'])) {
                    $lead_id = $_POST['lead_id'];
                    if ($_POST['filter_type_ro5'] != '') {
                        // $data['count'] = $this->db2->query(' select  sum(lead_id)  as lead_id from (  select count("lead_id") as lead_id from logs_post_data where lead_id ="'.$lead_id.'" and type = "'.$_POST['filter_type_ro5'].'" UNION ALL select count("lead_id") as lead_id from logs_docs where lead_id ="'.$lead_id.'" and type = "'.$_POST['filter_type_ro5'].'") e')->result();

                        $logs_post_data_table = ['Axis_redirection_post_data_request', 'Axis_redirection_post_data_decrypted'];
                        if (in_array($_POST['filter_type_ro5'], $logs_post_data_table)) {
                            $table = 'logs_post_data';
                        } else {
                            $table = 'logs_docs';
                        }

                        $data['count'] = $this->db2->query('select count("lead_id") as lead_id from ' . $table . ' where lead_id ="' . $lead_id . '" and type = "' . $_POST['filter_type_ro5'] . '"')->result();
                        //echo $this->db2->last_query();
                        $data['result'] = $this->db2->query('select req,res,lead_id,created_at,type from ' . $table . ' where lead_id ="' . $lead_id . '" AND type = "' . $_POST['filter_type_ro5'] . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page)->result();
                        //echo $this->db2->last_query();exit;
                    } else {
                        //without filter

                        $data1['count'] = $this->db2->query('select count("lead_id") as lead_id from logs_post_data where lead_id ="' . $lead_id . '"')->result();
                        //echo $this->db2->last_query();
                        $data2['count'] = $this->db2->query('select count("lead_id") as lead_id from logs_docs where lead_id ="' . $lead_id . '"')->result();
//echo $this->db2->last_query();
                        if ($data1['count'][0]->lead_id > $data2['count'][0]->lead_id) {
                            $config["total_rows"] = $data1['count'][0]->lead_id;
                        } else {
                            $config["total_rows"] = $data2['count'][0]->lead_id;
                        }
                        
                        /*$data['result'] = $this->db2->query('(select req,res,lead_id,created_at,type from logs_post_data where lead_id ="' . $lead_id . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page . ') UNION ALL (select req,res,lead_id,created_at,type from logs_docs where lead_id ="' . $lead_id . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page . ')')->result();*/
                        
                        $strSql = 'SELECT combine.*
                                      FROM ( SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               WHERE lead_id ="'.$lead_id .'"
                                               
                                              UNION
                                              SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                WHERE lead_id ="'.$lead_id .'"
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                //echo $strSql;die;  
                        $rsResult = $this->db2->query($strSql);
                        $data['result'] =  $rsResult->result();
                        
                        
                        //echo 'ss'.$config["total_rows"];exit;
                    }

                    //echo $this->db2->last_query();
                    //print_pre($data['count']);
                    //echo $data['count'][0]->lead_id;
                    //exit;
                    //$config["total_rows"] = $data['count'][0]->lead_id;
                } else {
                    $lead_id = $_POST['lead_id'];
                    $arr = explode('-', $this->input->post('dates'));
                    $start_date = str_replace('/', '-', $arr[0]);
                    $end_date = str_replace('/', '-', $arr[1]);

                    if ($this->input->post('time_to')) {

                        $start_date = $start_date . ' ' . $this->input->post('time_to') . ':0:0';
                    } else {
                        $start_date = $start_date . ' ' . '0:0:0';
                    }
                    if ($this->input->post('time_from')) {
                        $end_date = $end_date . ' ' . $this->input->post('time_from') . ':59:59';
                    } else {
                        $end_date = $end_date . ' ' . '23:59:59';
                    }

                    if ($_POST['filter_type_ro5'] != '') {
                        $logs_post_data_table = ['Axis_redirection_post_data_request', 'Axis_redirection_post_data_decrypted'];
                        if (in_array($_POST['filter_type_ro5'], $logs_post_data_table)) {
                            $table = 'logs_post_data';
                        } else {
                            $table = 'logs_docs';
                        }

                        $data['count'] = $this->db2->query('select count(lead_id) as lead_id from ' . $table . ' where  (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '") AND type ="' . $_POST['filter_type_ro5'] . '"')->result();
                        //echo $this->db2->last_query();
                        $data['result'] = $this->db2->query('select req,res,lead_id,type,created_at from ' . $table . ' where  (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ) AND type = "' . $_POST['filter_type_ro5'] . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page)->result();
//echo $this->db2->last_query();exit;
                    } else {
                        $data1['count'] = $this->db2->query('select count(lead_id) as lead_id from logs_post_data where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"')->result();
                        //echo $this->db2->last_query();
                        $data2['count'] = $this->db2->query('select count(lead_id) as lead_id from logs_docs where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"')->result();
                        //echo $this->db2->last_query();
                        if ($data1['count'][0]->lead_id > $data2['count'][0]->lead_id) {
                            $config["total_rows"] = $data1['count'][0]->lead_id;
                        } else {
                            $config["total_rows"] = $data2['count'][0]->lead_id;
                        }
                        
                        /*$data['result'] = $this->db2->query('(select req,res,lead_id,type,created_at from logs_post_data where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc LIMIT 10 OFFSET ' . $page . ' )UNION ALL (select req,res,lead_id,type,created_at from logs_docs where  created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '" ORDER BY created_at desc  LIMIT 10 OFFSET ' . $page . ')')->result();*/
                        
                        $strSql = 'SELECT combine.*
                                      FROM ( SELECT req,res,lead_id,created_at,type
                                                FROM logs_post_data
                                               WHERE created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '"
                                               AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
                                              UNION
                                              SELECT req,res,lead_id,created_at,type
                                                FROM logs_docs
                                                WHERE created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" 
                                                AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '"
                                            ) combine
                                            
                                     ORDER BY combine.created_at DESC LIMIT 10 OFFSET ' . $page . '';
                                     
                        $rsResult = $this->db2->query($strSql);
                        $data['result'] =  $rsResult->result();
                        
                        //echo $this->db2->last_query();
                        //echo 'ss'.$config["total_rows"];exit;
                    }
                    //echo $start_date;
                    //echo $this->db2->last_query();exit;
                }
            }
        }
        $this->load->library("pagination");
        //$config["total_rows"] = 20;
        $config["per_page"] = 10;
        $config["num_links"] = 5;
        $config["base_url"] = base_url() . "all_logs";
        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();
        if ($_POST['product_type'] == 'R03') {
            $data["query"] = $this->db->last_query();
        } else {
            $data["query"] = $this->db2->last_query();
        }

        $this->load->employee_template("log_all_view", $data);
    }

    function get_cancer_ghi_log($str = "", $subType = "") {
        $data['ghi'] = [];

        if ($subType == 1)
            $data['ghi'] = $this->db->where(["lead_id" => $str, "type" => "full_quote_request2"])->get("logs_docs")->result();
        if ($subType == 8)
            $data['ghi'] = $this->db->where(["lead_id" => $str, "type" => "cancer_request"])->get("logs_docs")->result();
        $data["query"] = $this->db->last_query();
        $this->load->employee_template("db_log_view", $data);
    }

    function get_confirmation($str = "", $product_type) {




        if ($product_type == 'R03') {
            $data['ghi'] = $this->db->where("lead_id", $str)
                            ->where_in('type', ['full_quote_request1', 'full_quote_request1_posH01', 'full_quote_request1_posH02', 'full_quote_request2', 'full_quote_request2_posH01', 'full_quote_request2_posH02', 'payment_request_abhicommonpgsourcelanding_post', 'payment_request_post', 'payment_response_post_check', 'bitly_url', 'payment_response_post'])
                            ->get("logs_docs")->result();
            //echo $this->db->last_query();exit;
        } else {
            //echo  2;
            //echo $this->db->last_query();exit;


            $query = 'select * from logs_docs where (lead_id = ' . $str . ') and  (type =  "full_quote_request1_retail_payment" or type = "full_quote_request2_retail_payment" or type = "coi_genarate" or type = "coi_uid_genarate" or type = "payment_request_post" or type = "payment_response_post")';
            $data['ghi'] = $this->db2->query($query)->result();
            //echo $this->db2->last_query();exit;
        }
        //echo 1;exit;

        if ($product_type == 'R03') {
            //echo 1;
            $data["query"] = $this->db->last_query();
            //print_pre($data);exit;
        } else {
            $data["query"] = $this->db2->last_query();
        }
        //print_Pre($data);exit;
        $this->load->employee_template("db_log_view", $data);
    }
    
    function get_sms_log($str = "", $product_type) {

        if ($product_type == 'R03') {
            $where = 'lead_id = "' . $str . '" and (type = "sms_logs_redirect" or type = "bitly_url" )';
            $data['ghi'] = $this->db->where($where)
                            ->get("logs_docs")->result();
            //  echo $this->db->last_query();
        } else {
            $where = 'lead_id = "' . $str . '" and (type = "sms_logs" or type = "bitly_url" )';
            $data['ghi'] = $this->db2->where($where)
                            ->get("logs_docs")->result();
        }

        if ($product_type == 'R03') {
            $data["query"] = $this->db->last_query();
        } else {
            $data["query"] = $this->db2->last_query();
        }
        //echo $this->db2->last_query();exit;
        $this->load->employee_template("sms_log_view", $data);
    }

    function get_omnidocs_log($str = "") {
        $data['ghi'] = $this->db->where(["lead_id" => $str, "type" => "OmniDocs"])->get("logs_docs")->result();
        $this->load->employee_template("db_log_view", $data);
    }
    
    /**
     * This Function is use for MIS View For D2C
     *
     * @param $i : Integer : Pagignation Count
     * 
     * @author Shardul Kulkarni<shardul.kulkarni@fyntune.com>
     * @return array of Records & HTML Listing View
     * @URL : http://eb.benefitz.in/d2c_mis_view
     */ 
    function get_d2c_mis_view($i){
        // SAMPLE DEMO START
            // Shardul CRM Addition Part Start
                // Create Lead In CRM Start
                //$emp_id = '2620';
                //$lead_id = json_decode($this->cron_m->createCRMLeadDropOff($emp_id),true);
                // Create Lead In CRM End
                
                // Create Member In CRM Start
                //$emp_id = "2642";
                //$lead_id['LeadId'] = '202009081231';
                //$this->cron_m->createCRMLeadDropOff($emp_id);
                //die("Die");
                //if(!empty($lead_id['LeadId'])) {
                    //$this->cron_m->insertMemberCRMDropOff($emp_id,$lead_id['LeadId']);
                //}
                // Create Member In CRM End 
            // Shardul CRM Addition Part End
            //die();
        // SAMPLE DEMO END
            
        // Define Variables
        $data = $data['result'] = $aMisviewdates = [];
        $strSqlExport = $sWhere = $user_stages = $user_stages_invald_data_redirection = $aMisviewdates = $start_date = $end_date = $time_to = $time_from = $lead_id = $mobile_number = $getLeadIdUsingMobileNumberData = '';
        
        // Page Title
        $data["title"] = "D2C MIS View"; 
        
        // Total Numbers of Records Display in Page
        $total_records_display_in_page = 10;
        $data['total_records_display_in_page'] = $total_records_display_in_page;
        
        // Pagignation Parameter in URL
        if ($i == 1) {
            $page = 0;
        } else {
            $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        }
        
        $mis_report_type = $data['mis_report_type'] = !empty($this->input->get('mis_report_type')) ? $this->input->get('mis_report_type') : 1;
        
        // When Search Form Submit below Condition Execuate
        if($this->input->get('search-mis')){
            
            $user_stages = $data['user_stages'] = ($this->input->get('user_stages')) ? (int)trim($this->input->get('user_stages')) : '';
            $user_stages_invald_data_redirection = $data['user_stages_invald_data_redirection'] = ($this->input->get('user_stages_invald_data_redirection')) ? trim($this->input->get('user_stages_invald_data_redirection')) : '';
            $aMisviewdates = $data['misviewdates'] = $this->input->get('misviewdates');
            $time_to  = $data['time_to'] =  ($this->input->get('time_to')) ? $this->input->get('time_to') : ''; 
            $time_from = $data['time_from'] =  ($this->input->get('time_from')) ? $this->input->get('time_from') : '';
            $lead_id = $data['lead_id'] =  ($this->input->get('lead_id')) ? $this->input->get('lead_id') : '';          
            $mobile_number = $data['mobile_number'] =  ($this->input->get('mobile_number')) ? $this->input->get('mobile_number') : '';  
            
        } else if($this->input->get('clear-search-filter')){
            $data['lead_id'] = $data['user_stages'] = $data['user_stages_invald_data_redirection'] = $data['misviewdates'] = $data['time_to'] = $data['time_from'] = $data['lead_id'] = $data['mobile_number'] = "";
        }
        
        // If Lead Id Present Below Condition Execuate
        $whereConditionMisView =  "";
        $leadIdDBVarName =  "";
        $mobileNoIdDBVarName = "";
        $tempDateVar = "";
        
        if ($lead_id) {
            $leadIdDBVarName = ($mis_report_type == 1 ? 'CRM_Lead_Id_No' : 'lead_id');
            $whereConditionMisView = ' WHERE '.$leadIdDBVarName.' ="' . $lead_id . '" ';
            
        } else if (!empty($mobile_number)) { // If Mobile number Entered Dont Consider Date Condiion
            $mobileNoIdDBVarName = ($mis_report_type == 1 ? 'mobile_no' : 'mobile_number');
            $whereConditionMisView = ' WHERE '.$mobileNoIdDBVarName.' ="' . $mobile_number . '" ';
        
        } else { 
            
            // Time And Date Validation
            $aMisviewdates = explode('-', $aMisviewdates);
            $start_date = str_replace('/', '-', $aMisviewdates[0]);
            $end_date = str_replace('/', '-', $aMisviewdates[1]);
            
            // Condition for Time To 
            if ($time_to) {             
                $start_date = $start_date . ' ' . $time_to . ':0:0';
            } else {
                $start_date = $start_date . ' ' . '0:0:0';
            }
            
            // Condition for Time From 
            if ($time_from) {               
                $end_date = $end_date . ' ' . $time_from . ':59:59';
            } else {
                $end_date = $end_date . ' ' . '23:59:59';
            }
            
            $tempDateVar = ($mis_report_type == 1 ? 'Login_Date' : 'created_date');
            
            $whereConditionMisView .= ' where '.$tempDateVar.' BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)).'" ';
        
            if($user_stages && $mis_report_type == 1){
                $whereConditionMisView .= ' AND User_Stages='.$user_stages;
            }

            if($user_stages_invald_data_redirection && $mis_report_type == 2){
                $whereConditionMisView .= ' AND error_message_type LIKE "%'.$user_stages_invald_data_redirection.'%" ';
            }           
                            
        }
        
        /*$aResultCount['count'] = $this->db2->query('select count("CRM_Lead_Id_No") as lead_id from AxisRetail_MISView '.$whereConditionMisView)->result();
        $strSql = 'SELECT * FROM AxisRetail_MISView '.$whereConditionMisView.' LIMIT 10 OFFSET ' . $page . '';  
        $strSqlExport = 'SELECT * FROM AxisRetail_MISView '.$whereConditionMisView;
        $config["total_rows"] = $aResultCount['count'][0]->lead_id;*/
        
        if($mis_report_type == 1) {
            $aResultCount['count'] = $this->db2->query('select count("CRM_Lead_Id_No") as lead_id from AxisRetail_MISView_app '.$whereConditionMisView)->result();
            $strSql = 'SELECT AxisRetail_MISView_app.*, "" AS error_message FROM AxisRetail_MISView_app '.$whereConditionMisView.' LIMIT 10 OFFSET ' . $page . '';  
            $strSqlExport = 'SELECT AxisRetail_MISView_app.*, "" AS error_message FROM AxisRetail_MISView_app '.$whereConditionMisView;
            $config["total_rows"] = $aResultCount['count'][0]->lead_id;
        } else {
            
            $inValidRedirectVariables = ',"" as Nominee_Declaration,"" as ID_TYPE,"" as Sum_Insured,"1 year" as Policy duration ,"" as familyConstruct,""  AS Proposer_Gender,"" as Proposer_Age,utm_campaign as UTM_Campaign,utm_medium as UTM_Medium,lead_id AS CRM_Lead_Id_No, salutation AS Salutation, client_name AS Client_Name, mobile_number AS mobile_no
            , "0" AS User_Stages, utm_source AS UTM_Source, Source_Id AS source_id, referral_code AS Referral_Code, product_name AS Product_Name
            , created_date AS Login_Date, modified_date AS modified_date, "" AS Premium_Amount, "" AS Client_ID, "" AS Application_No, "" AS Policy_No, "" AS Policy_Start
            , "" AS Policy_End, "" AS Payment_Status, "" AS Payment_Mode, "" AS Axis_Customer, lg_sol_id AS Branch_SOL_ID, imdcode AS IMDcode, error_message_type AS User_Stages_Status, error_message AS error_message,""  AS sp_id,"" AS employee_id';
            
            $aResultCount['count'] = $this->db2->query('select count("lead_id") as lead_id from redirection_invalid_data '.$whereConditionMisView)->result();
            $strSql = 'SELECT '.$inValidRedirectVariables.' FROM redirection_invalid_data '.$whereConditionMisView.' LIMIT 10 OFFSET ' . $page . '';    
            $strSqlExport = 'SELECT '.$inValidRedirectVariables.' FROM redirection_invalid_data '.$whereConditionMisView.' ORDER BY CRM_Lead_Id_No ASC';
            $config["total_rows"] = $aResultCount['count'][0]->lead_id;
        }
        
        // Get Result for Displaying Data on HTML Template Or View
        $rsResult = $this->db2->query($strSql);
        //print_pre($this->db2->last_query());exit;
        $data['result'] =  $rsResult->result(); 
        
        $this->load->library("pagination");
        //$config["total_rows"] = $aCount[0]['lead_count'];
        $config["per_page"] = $total_records_display_in_page;
        $config["num_links"] = 10;
        $config["base_url"] = base_url() . "d2c_mis_view";
        $this->pagination->initialize($config);

        // Pagignation CSS Start
        // https://www.kodingmadesimple.com/2015/04/php-codeigniter-pagination-twitter-bootstrap-styles.html
        $config['full_tag_open'] = '<ul class="pagination" style="padding-bottom: 5px;">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="w3-button">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="w3-button"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="w3-button">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="w3-button">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="w3-button">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="w3-button" style="color:#da8089;font-weight: bold;"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['reuse_query_string'] = true;
        // Pagignation CSS End
        
        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        
        // Below Condition Execuate When User Click On Export Report
        if ($this->input->get('export_excel-mis')) {
            $rsResult = $this->db2->query($strSqlExport);
            $aData =  $rsResult->result();
            $this->generateXls_MISVIEW($aData);
        }       
        
        // View Rendering
        $this->load->employee_template("d2c_mis_view", $data);
        
    }

    /**
     * This Function is use for Application Level Logs
     *
     * @param $i : Integer : Pagignation Count
     * 
     * @author Shardul Kulkarni<shardul.kulkarni@fyntune.com>
     * @return array of Records & HTML Listing View
     * @URL : http://eb.benefitz.in/all_logs
     */ 
    function all_logs($i){
        
        $data = array();
        $data["title"] = "Application Logs";    
        
        // Define Variables
        $data['result'] = $aCount = $aResult = [];
        $strSqlExport = $start_date = $end_date = $time_to = $time_from = $leadid = $mobile_number = $email_address = '';
        $product_type = $certificate_no = $filter_type_ro3 = $filter_type_ro5 = $sWhere =  '';  
        
        $data['email_address'] = $data['mobile_number'] = $data['lead_id'] = $data['applogdates'] = $data['time_to'] = $data['time_from'] = $data['product_type'] = $data['certificate_no'] = $data['filter_type_ro3'] = $data['filter_type_ro5'] = '';
        
        // Total Numbers of Records Display in Page
        $total_records_display_in_page = 10;
        $data['total_records_display_in_page'] = $total_records_display_in_page;
        
        // Pagignation Parameter in URL
        if ($i == 1) {
            $page = 0;
        } else {
            $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        }
    
        $product_type = $data['product_type'] = ($this->input->get('product_type')) ? trim($this->input->get('product_type')) : ''; 
        
        // When Search Form Submit below Condition Execuate
        if($this->input->get('search-applogs')){    
            $mobile_number = $data['mobile_number'] =  ($this->input->get('mobile_number')) ? $this->input->get('mobile_number') : '';
            $email_address = $data['email_address'] =  ($this->input->get('email_address')) ? $this->input->get('email_address') : '';
            $data['lead_id'] =  ($this->input->get('lead_id')) ? $this->input->get('lead_id') : '';
            $data['lead_id'] =  ($this->input->get('lead_id')) ? $this->input->get('lead_id') : '';
            $data['applogdates'] = $this->input->get('applogdates');
            $time_to  = $data['time_to'] =  ($this->input->get('time_to')) ? $this->input->get('time_to') : ''; 
            $time_from = $data['time_from'] =  ($this->input->get('time_from')) ? $this->input->get('time_from') : '';      
            $certificate_no = $data['certificate_no'] = ($this->input->get('certificate_no')) ? trim($this->input->get('certificate_no')) : '';
            $filter_type_ro3 = $data['filter_type_ro3'] = ($this->input->get('filter_type_ro3')) ? trim($this->input->get('filter_type_ro3')) : '';
            $filter_type_ro5 = $data['filter_type_ro5'] = ($this->input->get('filter_type_ro5')) ? trim($this->input->get('filter_type_ro5')) : '';
        
        }elseif($this->input->get('clear-search-filter')){ // This Condition Execuate When Clear form
            $data['email_address'] = $data['mobile_number'] = $data['lead_id'] = $data['applogdates'] = $data['time_to'] = $data['time_from'] = $data['product_type'] = $data['certificate_no'] = $data['filter_type_ro3'] = $data['filter_type_ro5'] = '';
        }
        
        // get Lead ID On the base Of Certification No
        if (!empty($certificate_no)) {
            
            if($product_type == 'R05' || $product_type == 'ABC' || $product_type == 'MUTHOOT' || $product_type == 'HERO_FINCORP' || $product_type == 'ABML'){
                
                $ghi_cert = $this->db2->select('ed.lead_id')->from('api_proposal_response apr')->join('employee_details ed', 'apr.emp_id = ed.emp_id')
                        ->where('apr.certificate_number', $certificate_no)->get()->row_array();
                        
                if (!empty($ghi_cert)) {                
                    $leadid =  $ghi_cert['lead_id'];
                }
            }else{
                
                $ghi_cert = $this->db->select('ed.lead_id')->from('api_cancer_response acr')->join('employee_details ed', 'acr.emp_id = ed.emp_id')
                        ->where('acr.COI_No', $certificate_no)->get()->row_array();     
                        
                if (empty($ghi_cert)) {             
                    
                    $ghi_cert = $this->db->select('ed.lead_id')->from('api_proposal_response apr')->join('employee_details ed', 'apr.emp_id = ed.emp_id')
                            ->where('apr.certificate_number', $certificate_no)->get()->row_array();
                }
                
                if (!empty($ghi_cert)) {                
                        $leadid =  $ghi_cert['lead_id'];
                }
            }   
        }       
        
        
        // Database Selection Condition
        $dbType = "db";
        if($product_type == 'R05' || $product_type == 'ABC' || $product_type == 'MUTHOOT' || $product_type == 'HERO_FINCORP' || $product_type == 'D01' || $product_type == 'D02' || $product_type == 'ABML'){
            $dbType = "db2";
        }
        
        // Fetch Lead Id Using Mobile Number
        if(!empty($mobile_number)) {
            $lead_id_using_mobile_number = $this->$dbType->select('ed.lead_id')->from('employee_details ed')->where('ed.mob_no', $mobile_number)->get()->result_array();
            
            $leadIdConnect = "";
            if (!empty($lead_id_using_mobile_number)) {             
                foreach($lead_id_using_mobile_number AS $dataTemp) {
                        if (!empty($dataTemp['lead_id'])) {
                            if(empty($leadIdConnect)) {
                                $leadIdConnect = "'".$dataTemp['lead_id']."'";
                            } else {
                                $leadIdConnect = $leadIdConnect.", '".$dataTemp['lead_id']."'";
                            }
                        }
                }
                $leadIdConnect = " AND lead_id IN (".$leadIdConnect.")";
            }
        }
        
        // Fetch Lead Id Using Email Address
        if(!empty($email_address)) {
            $lead_id_using_email_address = $this->$dbType->select('ed.lead_id')->from('employee_details ed')->where('ed.email', $email_address)->get()->result_array();
        
            $leadIdConnectEmail = "";
            if (!empty($lead_id_using_email_address)) {             
                foreach($lead_id_using_email_address AS $dataTemp) {
                        if (!empty($dataTemp['lead_id'])) {
                            if(empty($leadIdConnectEmail)) {
                                $leadIdConnectEmail = "'".$dataTemp['lead_id']."'";
                            } else {
                                $leadIdConnectEmail = $leadIdConnectEmail.", '".$dataTemp['lead_id']."'";
                            }
                        }
                }
                $leadIdConnectEmail = " AND lead_id IN (".$leadIdConnectEmail.")";
            }
        }
        
        // Manage Condition for Start Date, End Date, Time      
        $aMisviewdates = explode('-', $data['applogdates']);
        $start_date = str_replace('/', '-', $aMisviewdates[0]);
        $end_date = str_replace('/', '-', $aMisviewdates[1]);
        
        if ($time_to) {             
            $start_date = $start_date . ' ' . $time_to . ':0:0';
        } else {
            $start_date = $start_date . ' ' . '0:0:0';
        }
        if ($time_from) {               
            $end_date = $end_date . ' ' . $time_from . ':59:59';
        } else {
            $end_date = $end_date . ' ' . '23:59:59';
        }
                            
        $sWhere .= ' AND (created_at BETWEEN "' . date('Y-m-d H:i:s', strtotime($start_date)) . '" AND "' . date('Y-m-d H:i:s', strtotime($end_date)) . '") ';           
        
        // When Lead Id Come for certificate_no
        if (!empty($leadid)) {
            $sWhere  = " AND lead_id ='".$leadid."' ";
        }
        
        // When Lead Id Come from Search Text Box
        if (!empty($data['lead_id'])) {
            $sWhere  = " AND lead_id ='".$data['lead_id']."' ";
            
        }   
        
        // Mobile Number Lead Ids
        if (!empty($leadIdConnect)) {
            $sWhere  = $leadIdConnect;
            
        }
        // Email Lead Ids
        if (!empty($leadIdConnectEmail)) {
            $sWhere  = $leadIdConnectEmail;
        }
        
        // Set Condition for Filter for R03 & R05
        if ($filter_type_ro5 != '') {
            $sWhere  .= " AND type ='".$filter_type_ro5."' ";
        }elseif ($filter_type_ro3 != '') {
            $sWhere  .= " AND type = '".$filter_type_ro3."' ";
        }
        
        // Set Condition for Product Type According To Product.
        if($product_type=='R13' || $product_type=='R14'){

        }elseif (!empty($product_type) && $product_type != 'R05') {
            $sWhere  .= " AND product_id ='".$product_type."' ";
        }
        
        // Union Query for Data
        $unioAllQuery = 'SELECT req,res,lead_id,created_at,type FROM logs_post_data WHERE 1 '.$sWhere.'
                        UNION ALL
                        SELECT req,res,lead_id,created_at,type FROM logs_docs WHERE 1 '.$sWhere;
        //echo $unioAllQuery;exit;          
        // Query for getting all Records Count
        $sqlCount =  'SELECT count(combine.lead_id) as lead_count
                      FROM ( SELECT lead_id FROM logs_post_data WHERE 1 '.$sWhere.'
                             UNION ALL
                             SELECT lead_id FROM logs_docs WHERE 1 '.$sWhere.'
                            ) combine';
                
        // Query for Displaying Specific Number of Records
        $strSql = 'SELECT combine.* FROM ('.$unioAllQuery.') combine
                   ORDER BY combine.created_at DESC LIMIT '.$total_records_display_in_page.' OFFSET ' . $page . '';
        
        // Query for Export All Records
        $strSqlExport = 'SELECT combine.* FROM ('.$unioAllQuery.') combine  ORDER BY combine.created_at DESC';  
        
        // Fetch Records As per Product from Different Database  
        if($product_type == "R14"){
            $sqlCount='SELECT count(id) as lead_count from group_mod_create';
            $rsResultCount = $this->db->query($sqlCount);

            $strSql = 'SELECT `lead_id`, `created_at`, `type`, `req`, `res`
            FROM
            (
                
            SELECT `lead_id`, `created_at`, `type`, `req`, `res` FROM group_mod_logs

            UNION ALL

            SELECT `lead_id`, `created_at`, "Renewal_Check" as type, `req`, `res`  FROM group_mod_create

            UNION ALL

            SELECT `lead_id`, `created_at`, "Communication_API" as type, address_api_req as req, address_api_res as res  FROM group_mod_create

            )
            results
            where 1 '.$sWhere.' ORDER BY created_at DESC LIMIT '.$total_records_display_in_page.' OFFSET ' . $page . '';

            $rsResult = $this->db->query($strSql);
        }
        else   
        if($product_type=='R13'){
            $sqlCount='SELECT count(id) as lead_count from telesales_renewal_com_logs';
            $rsResultCount = $this->db->query($sqlCount);

            // $strSql = 'SELECT lead_id,created_at,id as type,req,res FROM telesales_renewal_com_logs where 1 '.$sWhere.' ORDER BY created_at DESC LIMIT '.$total_records_display_in_page.' OFFSET ' . $page . '';

            $strSql = 'SELECT `lead_id`, `created_at`, `type`, `req`, `res`
            FROM
            (SELECT lead_id,created_at,"SMS_AND_email" as type,req,res FROM telesales_renewal_com_logs
            UNION ALL
            SELECT lead_id,created_date AS created_at,"Renewal_Status" as type,req,res FROM telesales_renewal_logs
            UNION ALL
            SELECT lead_id,cron_date AS created_at,"customer_link_clicked" as type,req,res FROM telesales_renewal_cron_logs
            UNION ALL
            SELECT lead_id, created_at,"Member_Details" as type,req,res FROM tele_renewal_member_logs
            UNION ALL
            SELECT lead_id, created_at,"payment_response" as type,req,res FROM telesales_renewal_grp_payments
            UNION ALL
            SELECT lead_id, created_at,`type`,req,res FROM logs_docs_grp_renewal)
            results
            where 1 '.$sWhere.' ORDER BY created_at DESC LIMIT '.$total_records_display_in_page.' OFFSET ' . $page . '';

            $rsResult = $this->db->query($strSql);
        }elseif($product_type == 'R05' || $product_type == 'ABC' || $product_type == 'MUTHOOT' || $product_type == 'HERO_FINCORP' || $product_type == 'D01' || $product_type == 'D02' || $product_type == 'ABML'){ 
            $rsResultCount = $this->db2->query($sqlCount);
            $rsResult = $this->db2->query($strSql);
        }else{
            $rsResultCount = $this->db->query($sqlCount);                   
            $rsResult = $this->db->query($strSql);
        }
        
        // Record Count & Get Result Set
        $aCount = $rsResultCount->result_array();
        $aResult =  $rsResult->result();            
        
        // Get All Records  
        $data['result'] = $aResult;
            
        // CI Pagignation Start 
        $this->load->library("pagination");
        $config["total_rows"] = $aCount[0]['lead_count'];
        $config["per_page"] = $total_records_display_in_page;
        $config["num_links"] = 10;
        $config["base_url"] = base_url() . "all_logs";
        
        // Pagignation CSS Start
        // https://www.kodingmadesimple.com/2015/04/php-codeigniter-pagination-twitter-bootstrap-styles.html
        $config['full_tag_open'] = '<ul class="pagination" style="padding-bottom: 5px;">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="w3-button">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="w3-button"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="w3-button">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="w3-button">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="w3-button">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="w3-button" style="color:#da8089;font-weight: bold;"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['reuse_query_string'] = true;
        // Pagignation CSS End
        
        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        
        // Export Functionality Start       
        if ($this->input->get('export_excel-applogs')) {
            if($product_type == 'R13' || $product_type == 'R14'){
                $rsResult = $this->db->query($strSql);
            }elseif($product_type == 'R05' || $product_type == 'ABC' || $product_type == 'MUTHOOT' || $product_type == 'HERO_FINCORP' || $product_type == 'D01' || $product_type == 'D02' || $product_type == 'ABML'){
                $rsResult = $this->db2->query($strSqlExport);
            }else{
                $rsResult = $this->db->query($strSqlExport);
            }
            $aData =  $rsResult->result();  
            
            $this->generateXls_D2CAppLogs($aData);
        }   
        
        $this->load->employee_template("log_all_view", $data);
    }
    
    // create xlsx
    public function generateXls_MISVIEW($aData) {
        // create file name
        $fileName = 'mis-view-'.time().'.xls';  
        // load excel library
        $this->load->library('excel');
        $listInfo = $aData;
        $objPHPExcel = new PHPExcel();      
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(  PHPExcel_Style_NumberFormat::FORMAT_TEXT );
    
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'CRM_Lead_Id_No');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'User_Stages');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'User_Stages_Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Salutation');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'UTM_Source');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'source_id');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Referral_Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Client_Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Product_Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Login_Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Premium_Amount');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Client_ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Application_No');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Policy_No');
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Policy_Start');
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Policy_End');
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Payment_Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Payment_Mode');
        $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Axis_Customer');
        $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Branch_SOL_ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('U1', 'IMDcode');  
        $objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Mobile Number'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('X1', 'Error Message');    
        $objPHPExcel->getActiveSheet()->SetCellValue('Y1', 'SP Id');    
        $objPHPExcel->getActiveSheet()->SetCellValue('Z1', 'Employee Id');          
        
        // set Row
        $rowCount = 2;
        foreach ($listInfo as $list) {
            //$objPHPExcel->getActiveSheet()->getStyle('A1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $list->CRM_Lead_Id_No);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $list->User_Stages);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list->User_Stages_Status);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list->Salutation);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list->UTM_Source);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $list->source_id);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $list->Referral_Code);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $list->Client_Name);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $list->Product_Name);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $list->Login_Date);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $list->Premium_Amount);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $list->Client_ID);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $list->Application_No);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $list->Policy_No);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $list->Policy_Start);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $list->Policy_End);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $list->Payment_Status);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $list->Payment_Mode);
            $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $list->Axis_Customer);
            $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $list->Branch_SOL_ID);
            $objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $list->IMDcode);
            $objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, $list->mobile_no);
            $objPHPExcel->getActiveSheet()->SetCellValue('X' . $rowCount, $list->error_message);
            $objPHPExcel->getActiveSheet()->SetCellValue('Y' . $rowCount, $list->sp_id);
            $objPHPExcel->getActiveSheet()->SetCellValue('Z' . $rowCount, $list->employee_id);
            $rowCount++;
        }   
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
        $objWriter->save('php://output');exit();
    }

    function paystatus($i=''){

        $data = array();
        $data["title"] = "Application Logs"; 
     $data['result'] = [];
   if (isset($_REQUEST['proposal_status'])) {
        $unioAllQuery="SELECT p.emp_id,ed.lead_id,ed.emp_firstname,ed.emp_lastname,p.premium,p.sum_insured,p.status,e.product_name
FROM proposal AS p
LEFT JOIN employee_policy_detail AS e1 ON e1.policy_detail_id = p.policy_detail_id
LEFT JOIN product_master_with_subtype AS e ON e.id = e1.product_name
LEFT JOIN employee_details AS ed ON p.emp_id = ed.emp_id
WHERE emp_firstname IS NOT NULL and p.status='".$_REQUEST['proposal_status']."'";



   }
   else
   {
   $unioAllQuery= "SELECT p.emp_id,ed.lead_id,ed.emp_firstname,ed.emp_lastname,p.premium,p.sum_insured,p.status,e.product_name
FROM proposal AS p
LEFT JOIN employee_policy_detail AS e1 ON e1.policy_detail_id = p.policy_detail_id
LEFT JOIN product_master_with_subtype AS e ON e.id = e1.product_name
LEFT JOIN employee_details AS ed ON p.emp_id = ed.emp_id
WHERE emp_firstname IS NOT NULL";
   }

    $sqlquery="select distinct(status) from proposal";

    $querystatus=$this->db->query($sqlquery);
    $data['result_status']=$querystatus->result();

    
    $total_records_display_in_page = 10;
        $data['total_records_display_in_page'] = $total_records_display_in_page;
         if ($i == 1) {

            $page = 0;
        } else {


            $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;

        }

   $strSql = 'SELECT combine.* FROM ('.$unioAllQuery.') combine
             LIMIT '.$total_records_display_in_page.' OFFSET ' . $page . '';
            $query=$this->db->query($strSql);

        $data['result'] =  $query->result();
        $query1=$this->db->query($unioAllQuery);


         $num_rows=$query1->num_rows();

        // Total Numbers of Records Display in Page
       
        // echo "<script>alert('". $this->uri->segment(2)."')</script>";
        // Pagignation Parameter in URL
       

        // CI Pagignation Start 
        $this->load->library("pagination");
        $config["total_rows"] =$num_rows;
        $config["per_page"] = $total_records_display_in_page;

        $config["num_links"] = 10;
        $config["base_url"] = base_url() . "all_logs";
        
        // Pagignation CSS Start
        // https://www.kodingmadesimple.com/2015/04/php-codeigniter-pagination-twitter-bootstrap-styles.html
        $config['full_tag_open'] = '<ul class="pagination" style="padding-bottom: 5px;">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="w3-button">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="w3-button"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="w3-button">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="w3-button">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="w3-button">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="w3-button" style="color:#da8089;font-weight: bold;"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['reuse_query_string'] = true;
        // Pagignation CSS End
        

        $this->pagination->initialize($config);

        

        $data["links"] = $this->pagination->create_links();
        



       
       $this->load->employee_template('log_all_pstatus',$data);

 }


    

    public function keyword_search_recon()
    {
        // Database Selection Condition Start//
        $dbType = "db";
        if($_POST['product_type'] == 'R05' || $_POST['product_type'] == 'ABC')
        {
            $dbType = "db2";
        }
        // Database Selection Condition End//
    }


/**
* This Function is use for Recon Report Cover Selection On Click/Change Of Product Type 
*
* @author Prasad Pawar
* @return array of Records & HTML Listing View
* @URL : http://eb.benefitz.in/recon_report_apps
*/ 
    public function get_lead_data(){
        $pType = $_POST['pType'] ;
        $cover = $_POST['cover'] ;
        $recondates = $_POST['recondates'] ;
        $time_to = $_POST['time_to'] ;
        $time_from = $_POST['time_from'] ;
        $lead_id = $_POST['lead_id'] ;
        $currValue = $_POST['currValue'] ;
        
        $rowid = $_POST['rowid'];
        $rowperpage = $_POST['rowperpage'];
        //print_r($_POST);die();
        
        $conditions = array();
        if(!empty($currValue) && $currValue != 'All')
        {
            if($currValue == 'Payment Pending' || $currValue == 'Payment Received' || $currValue == 'Success'|| $currValue == 'Payment Rejected' || $currValue == 'Payment Rejected'){
                if($currValue == 'Payment Rejected'){
                    $currValue='Rejected';
                }
                $condition1[] = "p.status = '$currValue' ";
            }
        }
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            $conditions[] = "b.plan_code = '$t_plancode'";
            $condition1[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $condition1[] = "date(emp.created_at) BETWEEN '".date("Y-m-d",strtotime($s_date))."' AND '".date("Y-m-d",strtotime($e_date))."' ";
        }
            
        
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $condition1[] = "emp.product_id = '$pType";
            if($pType == 'ABC'){
                $conditions[] = "b.main_product_name = '$pType";
            }else{
            $conditions[] = "product_code = '$pType";
            }
        }
        
        if (count($condition1) > 0) {
            $sql_emp= " WHERE " . implode(' AND ', $condition1);
        }
        
        if (count($conditions) > 0) {
            
            $sql_cover = " WHERE " . implode(' AND ', $conditions);
            
        }
            
        if($pType == 'ABC' || $pType == 'R05'){
            $c_abc = "e.product_name = b.product_name ".$sql_cover."')";    
        }else{
            $c_abc = "e.product_name = b.id ".$sql_cover."')";  
            
        }
        $condition1[] = "emp.lead_id = '$lead_id'";
        if(!empty($lead_id)){
            if($pType == 'ABC' || $pType == 'R05'){
                $c_abc1 = "e.product_name = b.product_name )";  
            }else{
                $c_abc1 = "e.product_name = b.id )";    
                
            }
            
            
            $sql = "SELECT DISTINCT api.create_policy_type, api.certificate_number,p1.*,api.start_date,api.end_date FROM (SELECT t.*,t1.plan_code FROM 

(SELECT emp.lead_id,emp.created_at,emp.product_id,p.`status`,emp.emp_id,p.policy_detail_id,p.proposal_no,p.count as api_count,p.id AS proposal_id FROM proposal AS p  
LEFT JOIN employee_details AS emp ON emp.emp_id = p.emp_id where emp.lead_id = '$lead_id') AS t InNER JOIN 
(SELECT e.policy_detail_id,e.policy_no,b.product_code,b.plan_code 

FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON ".$c_abc1." AS t1 

ON t.policy_detail_id = t1.policy_detail_id) AS p1 LEFT JOIN api_proposal_response AS api ON p1.emp_id = api.emp_id 
AND p1.proposal_no = api.proposal_no_lead LIMIT ".$rowid.",".$rowperpage;
            
        }else{
            
            $sql = "SELECT DISTINCT api.create_policy_type, api.certificate_number,p1.*,api.start_date,api.end_date FROM (SELECT t.*,t1.plan_code FROM 

(SELECT emp.lead_id,emp.created_at,emp.product_id,p.`status`,emp.emp_id,p.policy_detail_id,p.proposal_no,p.count as api_count,p.id AS proposal_id FROM proposal AS p  
LEFT JOIN employee_details AS emp ON emp.emp_id = p.emp_id ".$sql_emp."') AS t InNER JOIN 
(SELECT e.policy_detail_id,e.policy_no,b.product_code,b.plan_code 

FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON ".$c_abc." AS t1 

ON t.policy_detail_id = t1.policy_detail_id) AS p1 LEFT JOIN api_proposal_response AS api ON p1.emp_id = api.emp_id 
AND p1.proposal_no = api.proposal_no_lead LIMIT ".$rowid.",".$rowperpage;
            
        }
        
    //echo $sql ;    
    $res = $this->$dbType->query($sql)->result_array();  
//print_pre($res);  
    foreach($res as $key => $val){
        
        $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
        WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
        $arr = $this->$dbType->query($q)->row_array();
        //print_pre($arr);          
        $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
        $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        $txn_sql = "SELECT payment_details.txndate FROM payment_details WHERE  payment_details.proposal_id = ".$val['proposal_id'];
        $txn_res = $this->$dbType->query($txn_sql)->row_array();
        $res[$key]['txn_res'] = $txn_res['txndate'];
        
    }
    //print_pre($res);exit;
    echo json_encode($res);
    exit;
            
    }
    
    public function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    
    public function get_grid_excel_old($pType,$cover ,$recondates,$time_to,$time_from,$lead_id,$currValue){
        
        //echo $currValue; exit();
        if($currValue == 'Payment Received'){
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            //$conditions1[] = "proposal.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "ed.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        
        if($dbType == "db2"){
            
            $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id". $t ." and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";
            
        }else{
            $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id". $t ." and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND p.EasyPay_PayU_status = 1 AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";   
        }
        

    
    //echo $sql ;
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;   
        }
    }       
    
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;   
        }else if($currValue == 'Success'){
            
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "ed.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        
        $sql = "SELECT distinct p.emp_id,p.count as api_count, p.policy_detail_id,ed.product_id,ed.lead_id,ed.created_at,apr.create_policy_type,apr.certificate_number,p.status,apr.end_date,apr.start_date
        from employee_details ed,payment_details as pd,proposal p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead where ed.emp_id = p.emp_id 
        and p.status in('Payment Received','Success') ".$t." and p.id = pd.proposal_id ";
    
    //echo $sql ;
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            $policy_detail_id = $val['policy_detail_id'] ;
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        
        }
    }   
            
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
        }
        else if($currValue == 'Mismatch Original'){
      
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions1[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions1[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions1[] = "emp.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions1[] = "emp.product_id = '$pType'";
        }
        if(count($conditions1)>0){
            $t = " AND " . implode(' AND ', $conditions1);
        }
        
        $sql = "SELECT emp.lead_id, p.policy_detail_id,p.count as api_count,a.certificate_number,b.certificate_number AS duplicate_cer,a.emp_id,b.emp_id AS duplicate_emp,
p.status,p.created_date,b.start_date,b.end_date FROM proposal p,api_proposal_response AS a, api_proposal_response AS b,employee_details emp
WHERE p.emp_id = a.emp_id AND p.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead ".$t;  
    
        //echo $sql ; exit();
    $res = $this->$dbType->query($sql)->result_array();
    //print_pre($res);
    if(!empty($res)){
        for($i=0; $i<count($res);$i++){
            $policy_detail_id = $res[$i]['policy_detail_id'] ;
            
            if(!empty($res[$i]['certificate_number'])){
            $q = "SELECT group_concat(distinct emp.lead_id) as duplicate_lead,group_concat(distinct emp.product_id) as duplicate_product FROM api_proposal_response INNER JOIN employee_details AS emp ON emp.emp_id = 
            api_proposal_response.emp_id WHERE api_proposal_response.emp_id !=".$res[$i]['emp_id']." AND certificate_number = '".$res[$i]['certificate_number']."'" ;
        
            $duplicate_res = $this->$dbType->query($q)->row_array();
            
            $res[$i]['duplicate_lead'] = $duplicate_res['duplicate_lead'];
            
                    
            $res[$i]['coi_duplicate_empids'] = $duplicate_res['duplicate_product']; 
            }else{
                $res[$i]['duplicate_lead'] = 'null';
            
                    
                $res[$i]['coi_duplicate_empids'] = 'null';
            }
            if(!empty($policy_detail_id)){  
                if($pType == 'R05' || $pType == 'ABC'){
                
                    $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
            
                }else{
                    $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
                
                }
                
                $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
                $res[$i]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
            }
            
            
            
            
            
        }
    }   
        
        
            
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
        }
        else if($currValue == 'Mismatch Policy'){

        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "e.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "e.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        
        
    $sql = "SELECT distinct p.count as api_count,p.policy_detail_id,apr.certificate_number,e.emp_id,e.product_id,e.lead_id,p1.status,apr.start_date,apr.end_date,p1.created_date from employee_details e,
 proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead 
 WHERE p.emp_id = e.emp_id AND p.status IN('Payment Received','Success') AND p.status<>p1.status ".$t;
    
    //echo $sql ; exit();
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            $policy_detail_id = $val['policy_detail_id'] ;
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;

        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        
        }
    }   
                
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
        }   
        else if($currValue == 'coi_success'||$currValue == 'coi_faliure'){
            
            $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions_coi[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        //echo "Break Point" ;  
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions_coi[] = "ed.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
            
        if(!empty($lead_id)){
            $conditions_coi[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions_coi[] = "ed.product_id = '$pType'";
        }
        $sql = "select DISTINCT apr.certificate_number as COI_no,ed.lead_id,p.policy_detail_id,p.emp_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd";
        if(count($conditions_coi) > 0) {
            if($currValue == 'coi_success'){
                $sql .= " WHERE " . implode(' AND ', $conditions_coi);
                $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')" ;
            }else if($currValue == 'coi_faliure'){
                $sql .= " WHERE " . implode(' AND ', $conditions_coi);
                $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')" ;
            }
        }
        //echo $sql; exit ;
     
    $res = $this->$dbType->query($sql)->result_array();
    foreach($res as $key => $val){
        
        $res[$key]['status'] = $res[$key]['payment_status'];
        $res[$key]['created_at'] = $res[$key]['payment_date'] ;
        
        $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
        WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
        $arr = $this->$dbType->query($q)->row_array();
        //print_pre($arr);          
        $res[$key]['coi_duplicate_empids'] = $arr['duplicate']; 
        $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;   
    }
    
    
            //print_pre($res);exit;
        /******************** For XL Purpose ***************************************/
            
            //$res['emp_id'] = $res['payment_date'];
            //print_pre($res);exit();
            $this->generateXls_Recon($res);
            echo json_encode($res);
        exit;
    
            
        }
        else
        {
        
        
        
        $conditions = array();
        if(!empty($currValue) && $currValue != 'All')
        {
            if($currValue == 'Payment Pending' || $currValue == 'Payment Received' || $currValue == 'Success'){
                $condition1[] = "p.status = '$currValue' ";
            }
        }
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            $conditions[] = "b.plan_code = '$t_plancode'";
            $condition1[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $condition1[] = "date(emp.created_at) BETWEEN '".date("Y-m-d",strtotime($s_date))."' AND '".date("Y-m-d",strtotime($e_date))."' ";
        }
            
        
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $condition1[] = "emp.product_id = '$pType";
            if($pType == 'ABC'){
                $conditions[] = "b.main_product_name = '$pType";
            }else{
            $conditions[] = "product_code = '$pType";
            }
        }
        
        if (count($condition1) > 0) {
            $sql_emp= " WHERE " . implode(' AND ', $condition1);
        }
        
        if (count($conditions) > 0) {
            
            $sql_cover = " WHERE " . implode(' AND ', $conditions);
            
        }
            
        if($pType == 'ABC' || $pType == 'R05'){
            $c_abc = "e.product_name = b.product_name ".$sql_cover."')";    
        }else{
            $c_abc = "e.product_name = b.id ".$sql_cover."')";  
            
        }
        $condition1[] = "emp.lead_id = '$lead_id'";
        if(!empty($lead_id)){
            if($pType == 'ABC' || $pType == 'R05'){
                $c_abc1 = "e.product_name = b.product_name )";  
            }else{
                $c_abc1 = "e.product_name = b.id )";    
                
            }
            
            
            $sql = "SELECT DISTINCT api.create_policy_type, api.certificate_number,p1.*,api.start_date,api.end_date FROM (SELECT t.*,t1.plan_code FROM 

(SELECT emp.lead_id,emp.created_at,emp.product_id,p.`status`,emp.emp_id,p.policy_detail_id,p.proposal_no,p.count as api_count FROM proposal AS p  
LEFT JOIN employee_details AS emp ON emp.emp_id = p.emp_id where emp.lead_id = '$lead_id') AS t InNER JOIN 
(SELECT e.policy_detail_id,e.policy_no,b.product_code,b.plan_code 

FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON ".$c_abc1." AS t1 

ON t.policy_detail_id = t1.policy_detail_id) AS p1 LEFT JOIN api_proposal_response AS api ON p1.emp_id = api.emp_id 
AND p1.proposal_no = api.proposal_no_lead" ;
            
        }else{
            
            $sql = "SELECT DISTINCT api.create_policy_type, api.certificate_number,p1.*,api.start_date,api.end_date FROM (SELECT t.*,t1.plan_code FROM 

(SELECT emp.lead_id,emp.created_at,emp.product_id,p.`status`,emp.emp_id,p.policy_detail_id,p.proposal_no,p.count as api_count FROM proposal AS p  
LEFT JOIN employee_details AS emp ON emp.emp_id = p.emp_id ".$sql_emp."') AS t InNER JOIN 
(SELECT e.policy_detail_id,e.policy_no,b.product_code,b.plan_code 

FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON ".$c_abc." AS t1 

ON t.policy_detail_id = t1.policy_detail_id) AS p1 LEFT JOIN api_proposal_response AS api ON p1.emp_id = api.emp_id 
AND p1.proposal_no = api.proposal_no_lead ";
            
        }
        
    //echo $sql ;    
    $res = $this->$dbType->query($sql)->result_array();  
//print_pre($res);  
    foreach($res as $key => $val){
        
        $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
        WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
        $arr = $this->$dbType->query($q)->row_array();
        //print_pre($arr);          
        $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
        $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        
    }
    $this->generateXls_Recon($res);
    echo json_encode($res);
    exit;
        
        }
            
    }
    
    
    
    public function get_grid_excel($pType,$cover ,$recondates,$time_to,$time_from,$lead_id,$currValue){
        
        //echo $currValue; exit();
        if($currValue == 'Payment Received'){
        
         $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            //$conditions1[] = "proposal.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "ed.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        
        if($dbType == "db2"){
            
            if(!empty($lead_id)){
                
                $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id and ed.lead_id = '$lead_id' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";
                
                
            }else{
                
                $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id". $t ." and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id";
                
            }
            
            
        }else{
            
            if(!empty($lead_id)){
                
            $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id and ed.lead_id = '$lead_id' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND p.EasyPay_PayU_status = 1 AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";
                
                
            }else{
                    
                $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id". $t ." and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND p.EasyPay_PayU_status = 1 AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";
 
            }
            
                
        }
        
    
    //echo $sql ;
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;   
        }
    }       
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
        }
        else if($currValue == 'Mismatch Original'){
      
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions1[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions1[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions1[] = "emp.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions1[] = "emp.product_id = '$pType'";
        }
        if(count($conditions1)>0){
            $t = " AND " . implode(' AND ', $conditions1);
        }
        
        $sql = "SELECT emp.lead_id,emp.product_id,a.create_policy_type, p.policy_detail_id,p.count as api_count,a.certificate_number,b.certificate_number AS duplicate_cer,a.emp_id,b.emp_id AS duplicate_emp,
p.status,p.created_date,b.start_date,b.end_date FROM proposal p,api_proposal_response AS a, api_proposal_response AS b,employee_details emp
WHERE p.emp_id = a.emp_id AND p.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead ".$t;  
    
        //echo $sql ; exit();
    $res = $this->$dbType->query($sql)->result_array();
    //print_pre($res);
    if(!empty($res)){
        for($i=0; $i<count($res);$i++){
            $policy_detail_id = $res[$i]['policy_detail_id'] ;
            
            if(!empty($res[$i]['certificate_number'])){
            $q = "SELECT group_concat(distinct emp.lead_id) as duplicate_lead,group_concat(distinct emp.product_id) as duplicate_product FROM api_proposal_response INNER JOIN employee_details AS emp ON emp.emp_id = 
            api_proposal_response.emp_id WHERE api_proposal_response.emp_id !=".$res[$i]['emp_id']." AND certificate_number = '".$res[$i]['certificate_number']."'" ;
        
            $duplicate_res = $this->$dbType->query($q)->row_array();
            
            $res[$i]['duplicate_lead'] = $duplicate_res['duplicate_lead'];
            
                    
            $res[$i]['coi_duplicate_empids'] = $duplicate_res['duplicate_product']; 
            }else{
                $res[$i]['duplicate_lead'] = 'null';
            
                    
                $res[$i]['coi_duplicate_empids'] = 'null';
            }
        if(!empty($policy_detail_id)){  
            if($pType == 'R05' || $pType == 'ABC'){
            
                $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
            }else{
                $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
            
            }
            
            $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
            $res[$i]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        }
            //echo $i;
            
            
            
            
        }
    }
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
        }
        else if($currValue == 'Mismatch Policy'){

        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "e.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "e.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        if(!empty($lead_id)){
            
            $sql = "SELECT distinct p.count as api_count,p.policy_detail_id,apr.create_policy_type,apr.certificate_number,e.emp_id,e.product_id,e.lead_id,p1.status,apr.start_date,apr.end_date,p1.created_date from employee_details e,
 proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead 
 WHERE p.emp_id = e.emp_id AND p.status IN('Payment Received','Success') AND p.status<>p1.status and e.lead_id = '$lead_id' ";
            
        }else{
        
            $sql = "SELECT distinct p.count as api_count,p.policy_detail_id,apr.create_policy_type,apr.certificate_number,e.emp_id,e.product_id,e.lead_id,p1.status,apr.start_date,apr.end_date,p1.created_date from employee_details e,
 proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead 
 WHERE p.emp_id = e.emp_id AND p.status IN('Payment Received','Success') AND p.status<>p1.status ".$t;
            
        }
        
    
    
    //echo $sql ; exit();
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            $policy_detail_id = $val['policy_detail_id'] ;
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;

        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        
        }
    }       
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
        }   
        else if($currValue == 'coi_success'||$currValue == 'coi_faliure'){
            
            $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions_coi[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions_coi[] = "ed.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
            
        if(!empty($lead_id)){
            $conditions_coi[] = "ed.lead_id = '$lead_id'";
            $conditions_coi1[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions_coi[] = "ed.product_id = '$pType'";
        }
        
        $sql = "select DISTINCT apr.create_policy_type,apr.certificate_number as COI_no,ed.product_id,ed.lead_id,p.policy_detail_id,p.count as api_count,p.emp_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd";
        
        if(!empty($lead_id)){
            
            if(count($conditions_coi1) > 0) {
                if($currValue == 'coi_success'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi1);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')  " ;
                }else if($currValue == 'coi_faliure'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi1);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')  ";
                }
            }
            
        }else{      
            
            if(count($conditions_coi) > 0) {
                if($currValue == 'coi_success'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')  ";
                }else if($currValue == 'coi_faliure'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')  " ;
                }
            }
        
        
        }
        
        
    //echo $sql ;    
    $res = $this->$dbType->query($sql)->result_array();
    foreach($res as $key => $val){
        $policy_detail_id = $val['policy_detail_id'] ;
        $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
        WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
        $arr = $this->$dbType->query($q)->row_array();
        //print_pre($arr);          
        $res[$key]['coi_duplicate_empids'] = $arr['duplicate']; 
        $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];  
    }
    //print_pre($res); exit();  
        $this->generateXls_Recon($res);
        echo json_encode($res);
        exit;
    
            
        }
        else
        {
        
        
        
        $conditions = array();
        if(!empty($currValue) && $currValue != 'All')
        {
            if($currValue == 'Payment Pending' || $currValue == 'Payment Received' || $currValue == 'Success'){
                $condition1[] = "p.status = '$currValue' ";
            }
        }
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            $conditions[] = "b.plan_code = '$t_plancode'";
            $condition1[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $condition1[] = "date(emp.created_at) BETWEEN '".date("Y-m-d",strtotime($s_date))."' AND '".date("Y-m-d",strtotime($e_date))."' ";
        }
            
        
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $condition1[] = "emp.product_id = '$pType";
            if($pType == 'ABC'){
                $conditions[] = "b.main_product_name = '$pType";
            }else{
            $conditions[] = "product_code = '$pType";
            }
        }
        
        if (count($condition1) > 0) {
            $sql_emp= " WHERE " . implode(' AND ', $condition1);
        }
        
        if (count($conditions) > 0) {
            
            $sql_cover = " WHERE " . implode(' AND ', $conditions);
            
        }
            
        if($pType == 'ABC' || $pType == 'R05'){
            $c_abc = "e.product_name = b.product_name ".$sql_cover."')";    
        }else{
            $c_abc = "e.product_name = b.id ".$sql_cover."')";  
            
        }
        $condition1[] = "emp.lead_id = '$lead_id'";
        if(!empty($lead_id)){
            if($pType == 'ABC' || $pType == 'R05'){
                $c_abc1 = "e.product_name = b.product_name )";  
            }else{
                $c_abc1 = "e.product_name = b.id )";    
                
            }
            
            
            $sql = "SELECT DISTINCT api.create_policy_type, api.certificate_number,p1.*,api.start_date,api.end_date FROM (SELECT t.*,t1.plan_code FROM 

(SELECT emp.lead_id,emp.created_at,emp.product_id,p.`status`,emp.emp_id,p.policy_detail_id,p.proposal_no,p.count as api_count,p.id AS proposal_id FROM proposal AS p  
LEFT JOIN employee_details AS emp ON emp.emp_id = p.emp_id where emp.lead_id = '$lead_id') AS t InNER JOIN 
(SELECT e.policy_detail_id,e.policy_no,b.product_code,b.plan_code 

FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON ".$c_abc1." AS t1 

ON t.policy_detail_id = t1.policy_detail_id) AS p1 LEFT JOIN api_proposal_response AS api ON p1.emp_id = api.emp_id 
AND p1.proposal_no = api.proposal_no_lead  ";
            
        }else{
            
            $sql = "SELECT DISTINCT api.create_policy_type, api.certificate_number,p1.*,api.start_date,api.end_date FROM (SELECT t.*,t1.plan_code FROM 

(SELECT emp.lead_id,emp.created_at,emp.product_id,p.`status`,emp.emp_id,p.policy_detail_id,p.proposal_no,p.count as api_count,p.id AS proposal_id FROM proposal AS p  
LEFT JOIN employee_details AS emp ON emp.emp_id = p.emp_id ".$sql_emp."') AS t InNER JOIN 
(SELECT e.policy_detail_id,e.policy_no,b.product_code,b.plan_code 

FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON ".$c_abc." AS t1 

ON t.policy_detail_id = t1.policy_detail_id) AS p1 LEFT JOIN api_proposal_response AS api ON p1.emp_id = api.emp_id 
AND p1.proposal_no = api.proposal_no_lead  ";
            
        }
        
    //echo $sql ;    
    $res = $this->$dbType->query($sql)->result_array();  
//print_pre($res);  
    foreach($res as $key => $val){
        
        $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
        WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
        $arr = $this->$dbType->query($q)->row_array();
        //print_pre($arr);          
        $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
        $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        $txn_sql = "SELECT payment_details.txndate FROM payment_details WHERE  payment_details.proposal_id = ".$val['proposal_id'];
        $txn_res = $this->$dbType->query($txn_sql)->row_array();
        $res[$key]['txn_res'] = $txn_res['txndate'];
        
    }
        
    $this->generateXls_Recon($res);
    echo json_encode($res);
    exit;
        
        }
            
    }
    
    
    
    public function get_mismatch_original_data(){
        $pType = $_POST['pType'] ;
        $cover = $_POST['cover'] ;
        $recondates = $_POST['recondates'] ;
        $time_to = $_POST['time_to'] ;
        $time_from = $_POST['time_from'] ;
        $lead_id = $_POST['lead_id'] ;
        $currValue = $_POST['currValue'] ;
        
        $rowid = $_POST['rowid'];
        $rowperpage = $_POST['rowperpage'];
        //print_r($_POST);die();
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions1[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions1[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions1[] = "emp.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions1[] = "emp.product_id = '$pType'";
        }
        if(count($conditions1)>0){
            $t = " AND " . implode(' AND ', $conditions1);
        }
        
        $sql = "SELECT emp.lead_id,emp.product_id,a.create_policy_type, p.policy_detail_id,p.count as api_count,a.certificate_number,b.certificate_number AS duplicate_cer,a.emp_id,b.emp_id AS duplicate_emp,
p.status,p.created_date,b.start_date,b.end_date FROM proposal p,api_proposal_response AS a, api_proposal_response AS b,employee_details emp
WHERE p.emp_id = a.emp_id AND p.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead ".$t."LIMIT ".$rowid.",".$rowperpage;  
    
        //echo $sql ; exit();
    $res = $this->$dbType->query($sql)->result_array();
    //print_pre($res);
    if(!empty($res)){
        for($i=0; $i<count($res);$i++){
            $policy_detail_id = $res[$i]['policy_detail_id'] ;
            
            if(!empty($res[$i]['certificate_number'])){
            $q = "SELECT group_concat(distinct emp.lead_id) as duplicate_lead,group_concat(distinct emp.product_id) as duplicate_product FROM api_proposal_response INNER JOIN employee_details AS emp ON emp.emp_id = 
            api_proposal_response.emp_id WHERE api_proposal_response.emp_id !=".$res[$i]['emp_id']." AND certificate_number = '".$res[$i]['certificate_number']."'" ;
        
            $duplicate_res = $this->$dbType->query($q)->row_array();
            
            $res[$i]['duplicate_lead'] = $duplicate_res['duplicate_lead'];
            
                    
            $res[$i]['coi_duplicate_empids'] = $duplicate_res['duplicate_product']; 
            }else{
                $res[$i]['duplicate_lead'] = 'null';
            
                    
                $res[$i]['coi_duplicate_empids'] = 'null';
            }
        if(!empty($policy_detail_id)){  
            if($pType == 'R05' || $pType == 'ABC'){
            
                $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
            }else{
                $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
            
            }
            
            $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
            $res[$i]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        }
            //echo $i;
            
            
            
            
        }
    }
    //print_pre($res); exit();
    echo json_encode($res);
    exit;
    
    
    
    }
    
    public function get_mismatch_data(){
        $pType = $_POST['pType'] ;
        $cover = $_POST['cover'] ;
        $recondates = $_POST['recondates'] ;
        $time_to = $_POST['time_to'] ;
        $time_from = $_POST['time_from'] ;
        $lead_id = $_POST['lead_id'] ;
        $currValue = $_POST['currValue'] ;
        
        $rowid = $_POST['rowid'];
        $rowperpage = $_POST['rowperpage'];
        //print_r($_POST);die();
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "e.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "e.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        if(!empty($lead_id)){
            
            $sql = "SELECT distinct p.count as api_count,p.policy_detail_id,apr.create_policy_type,apr.certificate_number,e.emp_id,e.product_id,e.lead_id,p1.status,apr.start_date,apr.end_date,p1.created_date from employee_details e,
 proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead 
 WHERE p.emp_id = e.emp_id AND p.status IN('Payment Received','Success') AND p.status<>p1.status and e.lead_id = '$lead_id' ";
            
        }else{
        
            $sql = "SELECT distinct p.count as api_count,p.policy_detail_id,apr.create_policy_type,apr.certificate_number,e.emp_id,e.product_id,e.lead_id,p1.status,apr.start_date,apr.end_date,p1.created_date from employee_details e,
 proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead 
 WHERE p.emp_id = e.emp_id AND p.status IN('Payment Received','Success') AND p.status<>p1.status ".$t." LIMIT ".$rowid.",".$rowperpage;
            
        }
        
    
    
    //echo $sql ; exit();
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            $policy_detail_id = $val['policy_detail_id'] ;
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;

        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        
        }
    }       
    echo json_encode($res);
    exit;
    
    }
    
    public function get_payment_redirect(){
        
        $pType = $_POST['pType'] ;
        $cover = $_POST['cover'] ;
        $recondates = $_POST['recondates'] ;
        $time_to = $_POST['time_to'] ;
        $time_from = $_POST['time_from'] ;
        $lead_id = $_POST['lead_id'] ;
        $currValue = $_POST['currValue'] ;
        
        $rowid = $_POST['rowid'];
        $rowperpage = $_POST['rowperpage'];
        //print_r($_POST);die();
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            //$conditions1[] = "proposal.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "ed.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        
        if($dbType == "db2"){
            
            if(!empty($lead_id)){
                
                $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id and ed.lead_id = '$lead_id' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";
                
                
            }else{
                
                $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id". $t ." and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id LIMIT ".$rowid.",".$rowperpage;
                
            }
            
            
        }else{
            
            if(!empty($lead_id)){
                
            $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id and ed.lead_id = '$lead_id' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND p.EasyPay_PayU_status = 1 AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id ";
                
                
            }else{
                    
                $sql = "SELECT ed.product_id, mpst.plan_code as arr_res_plan_code ,ed.lead_id,ed.emp_id,p.`status`,apr.create_policy_type,apr.certificate_number,p.count as api_count,p.status,apr.end_date,apr.start_date FROM 
product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
 proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead 
WHERE epd.product_name = mpst.id". $t ." and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND p.EasyPay_PayU_status = 1 AND 
 g.`status` = 'success' and p.status = 'Payment Pending' GROUP BY p.emp_id LIMIT ".$rowid.",".$rowperpage;
 
            }
            
                
        }
        
    
    //echo $sql ;
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;   
        }
    }       
    echo json_encode($res);
    exit;
        
    }
    public function get_payment_success(){
        
        $pType = $_POST['pType'] ;
        $cover = $_POST['cover'] ;
        $recondates = $_POST['recondates'] ;
        $time_to = $_POST['time_to'] ;
        $time_from = $_POST['time_from'] ;
        $lead_id = $_POST['lead_id'] ;
        $currValue = $_POST['currValue'] ;
        
        $rowid = $_POST['rowid'];
        $rowperpage = $_POST['rowperpage'];
        //print_r($_POST);die();
        
        $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions[] = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
        
            
        if(!empty($lead_id)){
            $conditions[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions[] = "ed.product_id = '$pType'";
        }
        if(count($conditions)>0){
            $t = " AND " . implode(' AND ', $conditions);
        }
        
        if(!empty($lead_id)){
            
            $sql = "SELECT distinct p.emp_id,p.count as api_count, p.policy_detail_id,ed.product_id,ed.lead_id,ed.created_at,apr.create_policy_type,apr.certificate_number,p.status,apr.end_date,apr.start_date
        from employee_details ed,payment_details as pd,proposal p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead where ed.emp_id = p.emp_id 
        and p.status in('Payment Received','Success') and ed.lead_id = '$lead_id' and p.id = pd.proposal_id ";
            
        }else{
            
            $sql = "SELECT distinct p.emp_id,p.count as api_count, p.policy_detail_id,ed.product_id,ed.lead_id,ed.created_at,apr.create_policy_type,apr.certificate_number,p.status,apr.end_date,apr.start_date
        from employee_details ed,payment_details as pd,proposal p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead where ed.emp_id = p.emp_id 
        and p.status in('Payment Received','Success') ".$t." and p.id = pd.proposal_id LIMIT ".$rowid.",".$rowperpage;  
            
        }
        
    
    //echo $sql ;
    $res = $this->$dbType->query($sql)->result_array();
    if(!empty($res)){
        foreach($res as $key => $val){
            $policy_detail_id = $val['policy_detail_id'] ;
            $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
            WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
            $arr = $this->$dbType->query($q)->row_array();
            //print_pre($arr);          
            $res[$key]['coi_duplicate_empids'] = $arr['duplicate'];
            $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];
        
        }
    }       
    echo json_encode($res);
    exit;
        
    }
    
    public function get_coi_data(){
            $pType = $_POST['pType'] ;
            $cover = $_POST['cover'] ;
            $recondates = $_POST['recondates'] ;
            $time_to = $_POST['time_to'] ;
            $time_from = $_POST['time_from'] ;
            $lead_id = $_POST['lead_id'] ;
            $currValue = $_POST['currValue'] ;
            
            $rowid = $_POST['rowid'];
            $rowperpage = $_POST['rowperpage'];
            //print_r($_POST);die();
            
            $conditions_coi = array();
        
        
        if(!empty($cover) && $cover != 'All')
        {
            $temp = explode('_', $cover);
            $t_policy_detail_id = $temp[0];
            $t_plancode = $temp[1];
            
            $conditions_coi[] = "p.policy_detail_id = '$t_policy_detail_id'";
        }
        
        if(!empty($recondates)){
            $date = explode('-', $recondates);
            $s_date = str_replace('/', '-', $date[0]);
            $e_date = str_replace('/', '-', $date[1]);
            
                //for time functinality
                if (!empty($time_to)) {             
                    $s_date = $s_date . ' ' . $time_to . ':0:0';
                } else {
                    $s_date = $s_date . ' ' . '0:0:0';
                }
                if (!empty($time_from)) {               
                    $e_date = $e_date . ' ' . $time_from . ':59:59';
                } else {
                    $e_date = $e_date . ' ' . '23:59:59';
                }
                
            $conditions_coi[] = "ed.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($s_date))."' AND '".date("Y-m-d H:i:s",strtotime($e_date))."' ";
           
        }
            
        if(!empty($lead_id)){
            $conditions_coi[] = "ed.lead_id = '$lead_id'";
            $conditions_coi1[] = "ed.lead_id = '$lead_id'";
        }
        $dbType = "db";
        if(!empty($pType)){
                
            if($pType == 'R05' || $pType == 'ABC'){
                $dbType = "db2";
            }
            $conditions_coi[] = "ed.product_id = '$pType'";
        }
        
        $sql = "select DISTINCT apr.create_policy_type,apr.certificate_number as COI_no,ed.product_id,ed.lead_id,p.policy_detail_id,p.count as api_count,p.emp_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd";
        
        if(!empty($lead_id)){
            
            if(count($conditions_coi1) > 0) {
                if($currValue == 'coi_success'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi1);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success') LIMIT ".$rowid.",".$rowperpage ;
                }else if($currValue == 'coi_faliure'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi1);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received') LIMIT ".$rowid.",".$rowperpage ;
                }
            }
            
        }else{      
            
            if(count($conditions_coi) > 0) {
                if($currValue == 'coi_success'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success') LIMIT ".$rowid.",".$rowperpage ;
                }else if($currValue == 'coi_faliure'){
                    $sql .= " WHERE " . implode(' AND ', $conditions_coi);
                    $sql .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received') LIMIT ".$rowid.",".$rowperpage ;
                }
            }
        
        
        }
        
        
    //echo $sql ;    
    $res = $this->$dbType->query($sql)->result_array();
    foreach($res as $key => $val){
        $policy_detail_id = $val['policy_detail_id'] ;
        $q = "SELECT group_concat(distinct e.lead_id) as duplicate FROM (SELECT b.emp_id,b.certificate_number FROM api_proposal_response AS b 
        WHERE b.certificate_number = '".$val['certificate_number']."' AND b.emp_id != '".$val['emp_id']."') AS p INNER JOIN employee_details AS e ON p.emp_id = e.emp_id" ;
        $arr = $this->$dbType->query($q)->row_array();
        //print_pre($arr);          
        $res[$key]['coi_duplicate_empids'] = $arr['duplicate']; 
        $q1 = "SELECT res FROM logs_docs WHERE res not LIKE '%<ErrorNumber>00%' and TYPE IN('full_quote_request2','full_quote_request1') and 
        lead_id = '".$val['lead_id']."' ORDER BY logs_docs.created_at DESC LIMIT 1";
        //echo $q1; 
        $arr_res_log = $this->$dbType->query($q1)->result_array();
        //print_pre($arr_res_log ); exit();
        $xml = "<ns0:GHI_Res xmlns:ns0=\"http:\/\/ABHICL_NBP_QuickQuote_Schemas.GHI_Response\"><errorObj><ErrorNumber>".$arr_res_log[0]['res'];
        $arr_error_log = $this->get_string_between($xml,'<ErrorMessage>', '<\/ErrorMessage>');
        $res[$key]['arr_error_log'] = $arr_error_log;
        
        if($pType == 'R05' || $pType == 'ABC'){
            
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }else{
            $sql_plan_code ="SELECT b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON b.id = e.product_name WHERE e.policy_detail_id = ".$policy_detail_id ;
        
        }
        $arr_res_plan_code = $this->$dbType->query($sql_plan_code)->row_array();
        $res[$key]['arr_res_plan_code'] = $arr_res_plan_code['plan_code'];  
    }
    //print_pre($res); exit();  
    echo json_encode($res);
    exit;
            
    }
    
    public function get_sub_product_type() 
    {

            // Database Selection Condition Start//
                $dbType = "db";
                if($_POST['product_type'] == 'R05' || $_POST['product_type'] == 'ABC'||$_POST['product_type'] == 'HERO_FINCORP')
                {
                    $dbType = "db2";
                }

            // Database Selection Condition End//
        if($_POST['product_type'] == 'ABC'||$_POST['product_type'] == 'HERO_FINCORP')
        {
            $querypn = "SELECT e.policy_detail_id,e.policy_no,b.product_code,b.policy_type_id,b.policy_subtype_id,b.product_name,b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON e.product_name = b.product_name WHERE b.main_product_name ='".$_POST['product_type']."' ";
            $data = $this->$dbType->query($querypn)->result_array();
            // if($_POST['product_type'] == 'ABC'){
                $html = '<option value="All">All</option>';
            // }
            $res = [];

            foreach($data as $cov)
            { 
                $s = $cov['product_name'];
                //$s = strtoupper(strstr($s, '_', true));
                $html .='<option value="'.$cov['policy_detail_id'].'_'.$cov['plan_code'].'">'.$s.'</option>';
            }
        }else if($_POST['product_type'] == 'R05'){
            $html .='<option value="1_4211">GHI</option>';
        }
        else
        {       
            $querypn = "SELECT e.policy_detail_id,e.policy_no,b.product_code,b.policy_type_id,b.policy_subtype_id,b.product_name,b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON e.product_name = b.id WHERE product_code ='".$_POST['product_type']."'";
            $data = $this->$dbType->query($querypn)->result_array();


            if($_POST['product_type'] == 'T03'||$_POST['product_type'] == 'R11'){
                $html='';
            }else{
                $html = '<option value="All" '.$scol.'>All</option>';
            }
            
            $res = [];
            
            foreach($data as $cov)
            { 
                $covpol=$cov['policy_detail_id'].'_'.$cov['plan_code'];                

                if($this->session->userdata('product_cover_types')==$covpol){
                    $scol='selected';
                }else{
                    $scol='';
                }

                if($cov['policy_detail_id'] == 403){
                    $html .='<option value="'.$cov['policy_detail_id'].'_'.$cov['plan_code'].'" '.$scol.'>GHI</option>';
                } else if($cov['policy_detail_id'] == 472||$cov['policy_detail_id'] == 475) {

                    $html .='<option value="'.$cov['policy_detail_id'].'_'.$cov['plan_code'].'" '.$scol.'>GHI_SUPERTOPUP</option>';
                }else{
                
                $s = $cov['policy_no'];
                if($cov['policy_detail_id']==415){
                    $s = 'GHI_TELESALE';
                }
                if($cov['policy_detail_id']==461){
                    $s = 'GCI_R10';
                }
                $s = strtoupper(strstr($s, '_', true));
                $html .='<option value="'.$cov['policy_detail_id'].'_'.$cov['plan_code'].'" '.$scol.'>'.$s.'</option>';
                }
            }
        }
            $res['html'] = $html;
            echo json_encode($res);
            exit;
    }

    
    /**
    * This Function is use for All Products Payments Status 
    *
    * @param $i : Integer : Pagignation Count
    * 
    * @author Prasad Pawar
    * @return array of Records & HTML Listing View
    * @URL : http://eb.benefitz.in/recon_report_apps
    */ 

    



    public function recon_report_apps($i)
    {
        
        //ini_set('display_errors', 1);
        $data = array();
        $data["title"] = "Recon Report";
        // print_pre($_POST);exit();
        if($_POST){
            $data['plan_arr'] = ['4211' => 'GHI','4224' => 'GP','4112' => 'GPA','4216' => 'GCI'];
            $by_product = isset($_POST['product_type']) ? $_POST['product_type'] : '';
            $by_cover = isset($_POST['product_cover_type']) ? $_POST['product_cover_type'] : ''; //policy_detail_id
            $by_date = isset($_POST['recondates']) ? $_POST['recondates'] : '';
            $by_to = isset($_POST['time_to']) ? $_POST['time_to'] : '';
            $by_from = isset($_POST['time_from']) ? $_POST['time_from'] : '';
            $by_lead = isset($_POST['lead_id']) ? $_POST['lead_id'] : '';
            
            $data['recon_product_type']=$_POST['product_type'];
            $this->session->set_userdata('product_cover_types',$_POST['product_cover_type']);
		 

 $dbType = "db";
if($by_product == 'R05' || $by_product == 'ABC'|| $by_product == 'HERO_FINCORP')
{
                    $dbType = "db2";
}

if((strtolower($by_cover)=='all'&&$by_product=='ABC')||(strtolower($by_cover)=='all'&&$by_product=='HERO_FINCORP')){
        $allpid=$this->$dbType->query("SELECT epd.policy_detail_id FROM product_master_with_subtype AS pmws JOIN employee_policy_detail AS epd ON pmws.product_name=epd.
        product_name WHERE pmws.main_product_name='$by_product' AND epd.product_type=1
        ")->result_array();

}else if($by_product=='R05'){

    $allpid=$this->$dbType->query("SELECT epd.policy_detail_id FROM product_master_with_subtype AS pmws JOIN employee_policy_detail AS epd ON pmws.id=epd.product_name WHERE pmws
    .product_code='".$by_product."'")->result_array();    

}else if(strtolower($by_cover)=='all'){

    $allpid=$this->db->query("SELECT epd.policy_detail_id FROM product_master_with_subtype AS pmws JOIN employee_policy_detail AS epd ON pmws.id=epd.product_name WHERE pmws
    .product_code='".$by_product."'")->result_array();

}

// print_r($allpid);exit;

 $getallpid='';
        foreach($allpid as $key => $value){
                //echo $allpid[$key]['policy_detail_id'];
                $getallpid .=$allpid[$key]['policy_detail_id'].",";
        }

        if(!empty($getallpid)){
            $newpid=rtrim($getallpid,",");

            $addpolicy_detail_id=" and p.policy_detail_id IN (".$newpid.") ";
    
        }

            
    $query = "SELECT COUNT(distinct employee_details.lead_id) AS total,proposal.policy_detail_id,proposal.status FROM proposal LEFT JOIN employee_details "
            . "ON proposal.emp_id = employee_details.emp_id ";
    $sql_totalLead = "SELECT COUNT(employee_details.emp_id) AS total_lead from employee_details" ;
    //COI success
    /*$sql_coi_success = "select DISTINCT apr.certificate_number as COI_no,ed.lead_id,p.policy_detail_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;*/
    $sql_coi_success = "select DISTINCT ed.lead_id,p.policy_detail_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    $sql_coi_success_total = "select DISTINCT ed.lead_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;

    $payment_pending = "select DISTINCT ed.lead_id,p.policy_detail_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    

    $payment_rejected = "select DISTINCT ed.lead_id,p.policy_detail_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;


    $sql_coi_faliure = "select DISTINCT apr.certificate_number as COI_no,ed.lead_id,p.policy_detail_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    $sql_coi_faliure_total = "select DISTINCT ed.lead_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    
    $conditions = array();
    $conditions_lead_id = array();        
            
            if(!empty($by_date)){
                $date = explode('-', $_REQUEST['recondates']);
                $datee = str_replace('/', '-', $date);
                $start_date = $datee[0];
                $end_date = $datee[1];

                //for time functinality
                if (!empty($by_to)) {             
                    $start_date = $start_date . ' ' . $by_to . ':0:0';
                } else {
                    $start_date = $start_date . ' ' . '0:0:0';
                }
                if (!empty($by_from)) {               
                    $end_date = $end_date . ' ' . $by_from . ':59:59';
                } else {
                    $end_date = $end_date . ' ' . '23:59:59';
                }
                $date = "p.created_date BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' ";
                $conditions_coi[] = $date ;
           
                $conditions[] = "employee_details.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' ";
                $conditions_lead_id[] =  "employee_details.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' ";
                
            }
            
            if(!empty($by_lead)){
                $data['by_lead'] = $by_lead ;
                $conditions[] = "employee_details.lead_id = '$by_lead'";
                $conditions_coi[] = "ed.lead_id = '$by_lead'";
            }
            $dbType = "db";
            if(!empty($by_product)){
                $data['prod_type'] = $by_product;
                
                if($by_product == 'R05' || $by_product == 'ABC'|| $by_product == 'HERO_FINCORP')
                {
                    $dbType = "db2";
                }
            $q = "SELECT e.policy_detail_id,e.policy_no,b.product_code,b.policy_type_id,b.policy_subtype_id,b.product_name,b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON e.product_name = b.id WHERE product_code ='".$_POST['product_type']."'" ;       
            if($by_product == 'ABC'||$by_product == 'HERO_FINCORP'){
                $q = "SELECT e.policy_detail_id,b.plan_code,b.product_name FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e on e.product_name = b.product_name WHERE b.main_product_name='$by_product'" ;
            }
            $result_temp1 = $this->$dbType->query($q)->result_array();
            $data['policy'] = $result_temp1 ;
            $conditions[] = "employee_details.product_id = '$by_product'";
            $conditions_coi[] = "ed.product_id = '$by_product'";
            $conditions_lead_id[] = "employee_details.product_id = '$by_product'" ;
        }
        $mismatch_cover_arr=array();
        for($i=0; $i<count($result_temp1 );$i++){
            $mismatch_cover_arr[$result_temp1[$i]['policy_detail_id']] = 0;
        }
        
            
            
            
            if(!empty($by_cover) && $by_cover != 'All'){
                $temp = explode('_', $by_cover);
                $t_policy_detail_id = $temp[0];
                $t_plancode = $temp[1];
                $conditions[] = "proposal.policy_detail_id = '$t_policy_detail_id'";
                
                $conditions_coi[] = "p.policy_detail_id = '$t_policy_detail_id'";
                $conditions_lead_id[] = "proposal.policy_detail_id = '$t_policy_detail_id'"; 
                //$sql_coi_success .= " and p.policy_detail_id in($t_policy_detail_id)";
                //$sql_coi_faliure .= " and p.policy_detail_id in($t_policy_detail_id)";
                $sql_totalLead .= " INNER JOIN proposal ON proposal.emp_id = employee_details.emp_id" ;
            }
            $conditions_lead = $conditions_lead_id; // conditions 
            //print_pre($conditions_lead); 
            if(!empty($by_lead)){
                
                $sql_totalLead .= " WHERE employee_details.lead_id = '$by_lead'";
            
            }
            else if (count($conditions_lead) > 0) {
              $sql_totalLead .= " WHERE " . implode(' AND ', $conditions_lead);
            }
            
            if (count($conditions_coi) > 0) {
              $sql_coi_success .= " WHERE " . implode(' AND ', $conditions_coi);
              $sql_coi_success .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')" ;



              $payment_pending .= " WHERE " . implode(' AND ', $conditions_coi);
              $payment_pending .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Pending')" ;


              $payment_rejected .= " WHERE " . implode(' AND ', $conditions_coi);
              $payment_rejected .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Rejected')" ;
              

               if(!empty($by_lead)){
                 $sql_coi_success_total .= " WHERE ed.lead_id = '$by_lead'";  
              }else{
                 $sql_coi_success_total .= " WHERE " . implode(' AND ', $conditions_coi);   
              }
              //echo $sql_coi_success_total."<br>"; 
              $sql_coi_success_total .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')" ;
              $sql_coi_faliure .= " WHERE " . implode(' AND ', $conditions_coi);
              $sql_coi_faliure .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')" ;
              if(!empty($by_lead)){
                $sql_coi_faliure_total .= " WHERE ed.lead_id = '$by_lead'";  
              }else{
                $sql_coi_faliure_total .= " WHERE " . implode(' AND ', $conditions_coi);  
              }
              
              $sql_coi_faliure_total .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')" ;
              
              $condition_mismatch = " AND " . implode(' AND ', $conditions_coi);
            }
            
  		$sql_coi_success_total .=$addpolicy_detail_id;          
       		$sql_coi_faliure_total .=$addpolicy_detail_id;
	      
            $data['b_cover'] = $by_cover;
            $sql = $query;
            if(!empty($by_lead)){
                $sql .= " WHERE employee_details.lead_id = '$by_lead'";

            }else{
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            if (count($conditions) > 0) {
              
              $incomplete_cond = " AND " . implode(' AND ', $conditions);
            }
            /******************************************** Mismatch ********************************************/
            
    
        if(!empty($by_lead)){
            $mismatch_sql = "SELECT distinct ed.lead_id from employee_details ed,proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON 
    p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead WHERE p.emp_id = ed.emp_id AND p.status IN('Payment Received','Success') AND 
    p.status<>p1.status and ed.lead_id = '$by_lead'";
        }else{
            
            $mismatch_sql = "SELECT distinct ed.lead_id from employee_details ed,proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON 
    p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead WHERE p.emp_id = ed.emp_id AND p.status IN('Payment Received','Success') AND 
    p.status<>p1.status ".$condition_mismatch;
            
        }   
        
    // echo $mismatch_sql;
    
            $mismatch_total = $this->$dbType->query($mismatch_sql)->result_array(); 
            
            /*for($i=0; $i<count($mismatch_total);$i++){
                
                $mismatch_sql2 = "SELECT policy_detail_id FROM proposal WHERE emp_id ='".$mismatch_total[$i]['emp_id']."' AND STATUS != 'Success'";
                //echo 
                $mismatch_cover_by = $this->$dbType->query($mismatch_sql2)->result_array();
                //echo $mismatch_sql2;;
                //print_r($mismatch_cover_by);
                for($j = 0 ; $j<count($mismatch_cover_by);$j++){
                    $mismatch_cover_arr[$mismatch_cover_by[$j]['policy_detail_id']]++;
                }
                
            }*/
            // Mismatch rename to Incomplete //
            //$data['mismatch_cover'] = $mismatch_cover_arr;
            $data['mismatch_total'] = count($mismatch_total);
        //************************************************ Payment to redirect ****************************************//
        
            if($dbType == "db2"){
                if(!empty($by_lead)){
                
/*                $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id and ed.lead_id = '$by_lead' AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;

*/
$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id WHERE ld.type='payment_request_post' AND pd.payment_status='Payment Pending' AND epd.product_name = mpst.id and ed.lead_id = '$by_lead' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' GROUP BY p.emp_id" ;

                }else{

/*                    $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;
  
*/

$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id  WHERE pd.payment_status='Payment Pending' AND epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success'  AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' And ". $date ." GROUP BY p.emp_id" ;

              }
                
            }else{
                if(!empty($by_lead)){
                
/*                $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id and ed.lead_id = '$by_lead' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
          GROUP BY p.emp_id" ;  
*/

$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id WHERE ld.type='payment_request_post' AND pd.payment_status='Payment Pending' AND epd.product_name = mpst.id and ed.lead_id = '$by_lead' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' GROUP BY p.emp_id" ;



                }else{
/*                    $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;
*/
$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id  WHERE pd.payment_status='Payment Pending' AND epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' And ". $date ." GROUP BY p.emp_id" ;



                }                
 
            }
            
         
        //echo $sql_payment_to_redirect;
            $data['payment_to_redirect_result'] = $this->$dbType->query($sql_payment_to_redirect)->result_array();

        //************************************************* paymment Success *******************************************//
        
        if(!empty($by_lead)){
                
            $sql_payment_success = "SELECT COUNT(distinct ed.lead_id) AS total, p.policy_detail_id from employee_details ed,proposal p where ed.emp_id = p.emp_id and p.status in('Payment Received','Success') 
 and ed.lead_id = '$by_lead' group BY p.policy_detail_id " ;    
        }else{
            $sql_payment_success = "SELECT COUNT(distinct ed.lead_id) AS total, p.policy_detail_id from employee_details ed,proposal p where ed.emp_id = p.emp_id and p.status in('Payment Received','Success') 
 and ed.product_id = '$by_product' and ". $date ." group BY p.policy_detail_id " ;
        }
        
        //  echo  $sql_payment_success;
        
        $data['payment_success_result'] = $this->$dbType->query($sql_payment_success)->result_array();  


        if(!empty($by_lead)){
                
            $sql_payment_success = "SELECT COUNT(distinct ed.lead_id) AS total, p.policy_detail_id from employee_details ed,proposal p where ed.emp_id = p.emp_id and p.status in('Payment Received','Success') 
 and ed.lead_id = '$by_lead' group BY p.policy_detail_id " ;    
        }else{
            $sql_payment_success = "SELECT COUNT(distinct ed.lead_id) AS total, p.policy_detail_id from employee_details ed,proposal p where ed.emp_id = p.emp_id and p.status in('Payment Received','Success') 
 and ed.product_id = '$by_product' and ". $date ." group BY p.policy_detail_id " ;
        }

        $data['payment_success_result'] = $this->$dbType->query($sql_payment_success)->result_array();  
        
        if(!empty($by_lead)){
                
            $sql_payment_rejected = "SELECT COUNT(distinct ed.lead_id) AS total, p.policy_detail_id from employee_details ed,proposal p where ed.emp_id = p.emp_id and p.status NOT IN('Payment Received','Success') 
 and ed.lead_id = '$by_lead' group BY p.policy_detail_id " ;    
        }else{
            $sql_payment_rejected = "SELECT COUNT(distinct ed.lead_id) AS total, p.policy_detail_id from employee_details ed,proposal p where ed.emp_id = p.emp_id and p.status NOT IN('Payment Received','Success') 
 and ed.product_id = '$by_product' and ". $date ." group BY p.policy_detail_id " ;
        }

        $data['payment_success_rejected'] = $this->$dbType->query($sql_payment_rejected)->result_array();  
        

            //Incomple rename to mismatch
            
        if(!empty($by_lead)){
            
            $sql_incomplete = "SELECT count(*) as count FROM proposal,api_proposal_response AS a, api_proposal_response AS b,employee_details
WHERE employee_details.emp_id=proposal.emp_id and proposal.emp_id = a.emp_id AND proposal.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead and employee_details.lead_id = '$by_lead'";
    
        }else{
            $sql_incomplete = "SELECT count(*) as count FROM proposal,api_proposal_response AS a, api_proposal_response AS b,employee_details
WHERE employee_details.emp_id=proposal.emp_id and proposal.emp_id = a.emp_id AND proposal.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead ".$incomplete_cond; 
        }               
    
            // echo $sql_incomplete ; 
            $incomplete_result = $this->$dbType->query($sql_incomplete)->row_array(); 
            $data['incomplete_result'] = $incomplete_result;    
            
            $s1 = $sql ;
            $s2 = $sql ;
            
            //echo $sql_totalLead;
        //---------------for payment status-------------------- //
            $s1 .= 'GROUP BY proposal.status';
            // echo $s1;
            $payment_result = $this->$dbType->query($s1)->result_array();  

            // print_r($s1);

            $data['payment_result'] = $payment_result ;
            // print_r($this->db2->last_query());exit;
            $s2 .= "GROUP BY proposal.status,proposal.policy_detail_id" ;
            // echo $s2.";";
            $payment_result_by_policy = $this->$dbType->query($s2)->result_array();  
            // echo $s1.";";
            // echo "<br>";
            // echo $s2.";";

            $data['payment_result_by_policy'] = $payment_result_by_policy ;
        //---------------for policy_detail_id -------------------- //
            $sql .= 'GROUP BY proposal.policy_detail_id';
            // echo $sql.";";
            $result = $this->$dbType->query($sql)->result_array(); 
            $data['result'] = $result ;
        //------------------------ Total Lead -----------------------------//
        //echo $sql_totalLead ;die();
        if(!empty($by_lead)){
            $getlead= " AND employee_details.lead_id = '$by_lead'";
        }else{
            $getlead="";
        }
        
        $sql_totalLead="SELECT COUNT(DISTINCT employee_details.emp_id) AS total_lead from employee_details WHERE employee_details.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' AND employee_details.product_id = '".$by_product."' $getlead ";

		// echo $sql_totalLead ;        

        $result_totalLead = $this->$dbType->query($sql_totalLead)->result_array(); 
        $data['total_lead'] = $result_totalLead ;
        //------------------------ Coi -----------------------------//
        $result_coi_faliure = $this->$dbType->query($sql_coi_faliure)->result_array(); 
        $data['result_coi_faliure'] = $result_coi_faliure ;
        $result_coi_faliure_total = $this->$dbType->query($sql_coi_faliure_total)->result_array(); 
        $data['result_coi_faliure_total'] = $result_coi_faliure_total ;
        
        $result_coi_success = $this->$dbType->query($sql_coi_success)->result_array(); 
        // echo $sql_coi_success.";";
        $data['result_coi_success'] = $result_coi_success ;

        // echo $payment_pending;

        $result_payment_pending = $this->$dbType->query($payment_pending)->result_array(); 

        $data['result_payment_pending'] = $result_payment_pending;


        // echo $payment_rejected;
        $result_payment_rejected = $this->$dbType->query($payment_rejected)->result_array(); 

        $data['result_payment_rejected'] = $result_payment_rejected;

        
        // print_r($this->db->last_query());exit;

        $result_coi_success_total = $this->$dbType->query($sql_coi_success_total)->result_array(); 

        // echo $sql_coi_success_total.";";
        // echo "<br>";
        // print_r($result_coi_success_total);
        // print_r($this->db->last_query());
        // exit;

        $data['result_coi_success_total'] = $result_coi_success_total ;
        //print_pre($data);

        }
        if($_POST['export_recon-applogs']==1){
            //print_pre($_POST);die();
            $this->get_grid_excel($_POST['product_type'],$_POST['product_cover_type'],$_POST['recondates'],$_POST['time_to'],$_POST['time_from'],$_POST['lead_id'],$_POST['view_button']);
        }
        
        $this->load->employee_template('recon_report_view',$data);
        
    }
   




    public function recon_report_apps_old($i)
    {
        
        //ini_set('display_errors', 1);
        $data = array();
        $data["title"] = "Recon Report";
        //print_pre($_POST);exit();
        if($_POST){
            $data['plan_arr'] = ['4211' => 'GHI','4224' => 'GP','4112' => 'GPA','4216' => 'GCI'];
            $by_product = isset($_POST['product_type']) ? $_POST['product_type'] : '';
            $by_cover = isset($_POST['product_cover_type']) ? $_POST['product_cover_type'] : ''; //policy_detail_id
            $by_date = isset($_POST['recondates']) ? $_POST['recondates'] : '';
            $by_to = isset($_POST['time_to']) ? $_POST['time_to'] : '';
            $by_from = isset($_POST['time_from']) ? $_POST['time_from'] : '';
            $by_lead = isset($_POST['lead_id']) ? $_POST['lead_id'] : '';

            $data['recon_product_type']=$_POST['product_type'];
            $this->session->set_userdata('product_cover_types',$_POST['product_cover_type']);

    $query = "SELECT COUNT(distinct employee_details.lead_id) AS total,proposal.policy_detail_id,proposal.status FROM proposal LEFT JOIN employee_details "
            . "ON proposal.emp_id = employee_details.emp_id ";
    $sql_totalLead = "SELECT COUNT(employee_details.emp_id) AS total_lead from employee_details" ;
    //COI success
    /*$sql_coi_success = "select DISTINCT apr.certificate_number as COI_no,ed.lead_id,p.policy_detail_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;*/
    $sql_coi_success = "select DISTINCT ed.lead_id,p.policy_detail_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    $sql_coi_success_total = "select DISTINCT ed.lead_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    
    $sql_coi_faliure = "select DISTINCT apr.certificate_number as COI_no,ed.lead_id,p.policy_detail_id,p.status as payment_status,pd.txndate AS payment_date,
    p.multi_status,ed.cust_id,apr.CustomerID,apr.start_date,apr.end_date from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    $sql_coi_faliure_total = "select DISTINCT ed.lead_id from employee_details as ed,proposal as p left join api_proposal_response as apr 
    on p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id,payment_details as pd" ;
    
    $conditions = array();
    $conditions_lead_id = array();        
            
            if(!empty($by_date)){
                $date = explode('-', $_REQUEST['recondates']);
                $datee = str_replace('/', '-', $date);
                $start_date = $datee[0];
                $end_date = $datee[1];

                //for time functinality
                if (!empty($by_to)) {             
                    $start_date = $start_date . ' ' . $by_to . ':0:0';
                } else {
                    $start_date = $start_date . ' ' . '0:0:0';
                }
                if (!empty($by_from)) {               
                    $end_date = $end_date . ' ' . $by_from . ':59:59';
                } else {
                    $end_date = $end_date . ' ' . '23:59:59';
                }
                $date = "ed.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' ";
                $conditions_coi[] = $date ;
           
                $conditions[] = "employee_details.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' ";
                $conditions_lead_id[] =  "employee_details.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' ";
                
            }
            
            if(!empty($by_lead)){
                $data['by_lead'] = $by_lead ;
                $conditions[] = "employee_details.lead_id = '$by_lead'";
                $conditions_coi[] = "ed.lead_id = '$by_lead'";
            }
            $dbType = "db";
            if(!empty($by_product)){
                $data['prod_type'] = $by_product;
                
                if($by_product == 'R05' || $by_product == 'ABC'||$by_product == 'HERO_FINCORP')
                {
                    $dbType = "db2";
                }
            $q = "SELECT e.policy_detail_id,e.policy_no,b.product_code,b.policy_type_id,b.policy_subtype_id,b.product_name,b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON e.product_name = b.id WHERE product_code ='".$_POST['product_type']."'" ;       
            if($by_product == 'ABC'||$by_product == 'HERO_FINCORP' ){
                $q = "SELECT e.policy_detail_id,b.plan_code,b.product_name FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e on e.product_name = b.product_name WHERE b.main_product_name='$by_product'" ;
            }
            $result_temp1 = $this->$dbType->query($q)->result_array();
            $data['policy'] = $result_temp1 ;
            $conditions[] = "employee_details.product_id = '$by_product'";
            $conditions_coi[] = "ed.product_id = '$by_product'";
            $conditions_lead_id[] = "employee_details.product_id = '$by_product'" ;
        }
        $mismatch_cover_arr=array();
        for($i=0; $i<count($result_temp1 );$i++){
            $mismatch_cover_arr[$result_temp1[$i]['policy_detail_id']] = 0;
        }
        
            
            
            
            if(!empty($by_cover) && $by_cover != 'All'){
                $temp = explode('_', $by_cover);
                $t_policy_detail_id = $temp[0];
                $t_plancode = $temp[1];
                $conditions[] = "proposal.policy_detail_id = '$t_policy_detail_id'";
                
                $conditions_coi[] = "p.policy_detail_id = '$t_policy_detail_id'";
                $conditions_lead_id[] = "proposal.policy_detail_id = '$t_policy_detail_id'"; 
                //$sql_coi_success .= " and p.policy_detail_id in($t_policy_detail_id)";
                //$sql_coi_faliure .= " and p.policy_detail_id in($t_policy_detail_id)";
                $sql_totalLead .= " INNER JOIN proposal ON proposal.emp_id = employee_details.emp_id" ;
            }
            $conditions_lead = $conditions_lead_id; // conditions 
            //print_pre($conditions_lead); 
            if(!empty($by_lead)){
                
                $sql_totalLead .= " WHERE employee_details.lead_id = '$by_lead'";
            
            }
            else if (count($conditions_lead) > 0) {
              $sql_totalLead .= " WHERE " . implode(' AND ', $conditions_lead);
            }
            
            if (count($conditions_coi) > 0) {
              $sql_coi_success .= " WHERE " . implode(' AND ', $conditions_coi);
              $sql_coi_success .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')" ;
               if(!empty($by_lead)){
                 $sql_coi_success_total .= " WHERE ed.lead_id = '$by_lead'";  
              }else{
                 $sql_coi_success_total .= " WHERE " . implode(' AND ', $conditions_coi);   
              }
              //echo $sql_coi_success_total."<br>"; 
              $sql_coi_success_total .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Success')" ;
              $sql_coi_faliure .= " WHERE " . implode(' AND ', $conditions_coi);
              $sql_coi_faliure .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')" ;
              if(!empty($by_lead)){
                $sql_coi_faliure_total .= " WHERE ed.lead_id = '$by_lead'";  
              }else{
                $sql_coi_faliure_total .= " WHERE " . implode(' AND ', $conditions_coi);  
              }
              
              $sql_coi_faliure_total .= " AND ed.emp_id = p.emp_id and p.id = pd.proposal_id and p.status in('Payment Received')" ;
              
              $condition_mismatch = " AND " . implode(' AND ', $conditions_coi);
            }
            
            
            
            $data['b_cover'] = $by_cover;
            $sql = $query;
            if(!empty($by_lead)){
                $sql .= " WHERE employee_details.lead_id = '$by_lead'";
                
                if($by_product=='T03'){
                    $sql .=" AND proposal.policy_detail_id = '$t_policy_detail_id'";
                }

            }else{
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            if (count($conditions) > 0) {
              
              $incomplete_cond = " AND " . implode(' AND ', $conditions);
            }
            /******************************************** Mismatch ********************************************/
            
    
        if(!empty($by_lead)){
            $mismatch_sql = "SELECT distinct ed.lead_id from employee_details ed,proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON 
    p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead WHERE p.emp_id = ed.emp_id AND p.status IN('Payment Received','Success') AND 
    p.status<>p1.status and ed.lead_id = '$by_lead'";
        }else{
            
            $mismatch_sql = "SELECT distinct ed.lead_id from employee_details ed,proposal p INNER JOIN proposal p1 ON p.emp_id = p1.emp_id LEFT JOIN api_proposal_response apr ON 
    p1.emp_id = apr.emp_id and p1.proposal_no = apr.proposal_no_lead WHERE p.emp_id = ed.emp_id AND p.status IN('Payment Received','Success') AND 
    p.status<>p1.status ".$condition_mismatch;
            
        }   
        
    // echo $mismatch_sql;
    
            $mismatch_total = $this->$dbType->query($mismatch_sql)->result_array(); 
            
            /*for($i=0; $i<count($mismatch_total);$i++){
                
                $mismatch_sql2 = "SELECT policy_detail_id FROM proposal WHERE emp_id ='".$mismatch_total[$i]['emp_id']."' AND STATUS != 'Success'";
                //echo 
                $mismatch_cover_by = $this->$dbType->query($mismatch_sql2)->result_array();
                //echo $mismatch_sql2;;
                //print_r($mismatch_cover_by);
                for($j = 0 ; $j<count($mismatch_cover_by);$j++){
                    $mismatch_cover_arr[$mismatch_cover_by[$j]['policy_detail_id']]++;
                }
                
            }*/
            // Mismatch rename to Incomplete //
            //$data['mismatch_cover'] = $mismatch_cover_arr;
            $data['mismatch_total'] = count($mismatch_total);
        //************************************************ Payment to redirect ****************************************//
        
            if($dbType == "db2"){
                if(!empty($by_lead)){
/*                $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id and ed.lead_id = '$by_lead' AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;

*/
$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id WHERE ld.type='payment_request_post' AND pd.payment_status='Payment Pending' AND epd.product_name = mpst.id and ed.lead_id = '$by_lead' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' GROUP BY p.emp_id" ;

                }else{

/*                    $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;
  
*/

$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id  WHERE pd.payment_status='Payment Pending' AND epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' And ". $date ." GROUP BY p.emp_id" ;


                }
                
            }else{
                if(!empty($by_lead)){
                
/*                $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id and ed.lead_id = '$by_lead' AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;

*/
$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id WHERE ld.type='payment_request_post' AND pd.payment_status='Payment Pending' AND epd.product_name = mpst.id and ed.lead_id = '$by_lead' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' GROUP BY p.emp_id" ;

                }else{
/*                    $sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,ghi_quick_quote_response AS g,
         proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead WHERE epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND ed.emp_id=p.emp_id and p.status = 'Payment Pending'
         And ". $date ." GROUP BY p.emp_id" ;
  
*/

$sql_payment_to_redirect = "SELECT ed.emp_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed inner join logs_docs as ld on ld.lead_id=ed.lead_id,ghi_quick_quote_response AS g,proposal AS p left join api_proposal_response as apr ON p.proposal_no = apr.proposal_no_lead left join payment_details as pd on p.id = pd.proposal_id  WHERE pd.payment_status='Payment Pending' AND epd.product_name = mpst.id AND ed.product_id = '$by_product' and p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND g.`status` = 'success' AND p.EasyPay_PayU_status = 1 AND ed.emp_id=p.emp_id and p.status = 'Payment Pending' And ". $date ." GROUP BY p.emp_id" ;


                }
                
 
            }
            
         
        echo $sql_payment_to_redirect;
            $data['payment_to_redirect_result'] = $this->$dbType->query($sql_payment_to_redirect)->result_array();

        //************************************************* paymment Success *******************************************//
        
        

            
            
            //Incomple rename to mismatch
            
        if(!empty($by_lead)){
            
            $sql_incomplete = "SELECT count(*) as count FROM proposal,api_proposal_response AS a, api_proposal_response AS b,employee_details
WHERE employee_details.emp_id=proposal.emp_id and proposal.emp_id = a.emp_id AND proposal.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead and employee_details.lead_id = '$by_lead' " ;
    
        }else{
            $sql_incomplete = "SELECT count(*) as count FROM proposal,api_proposal_response AS a, api_proposal_response AS b,employee_details
WHERE employee_details.emp_id=proposal.emp_id and proposal.emp_id = a.emp_id AND proposal.proposal_no = a.proposal_no_lead AND a.certificate_number = b.certificate_number AND a.emp_id != b.emp_id 
AND a.proposal_no_lead != b.proposal_no_lead ".$incomplete_cond; 
        }               
    
            echo $sql_incomplete ; exit();

            $incomplete_result = $this->$dbType->query($sql_incomplete)->row_array(); 
            $data['incomplete_result'] = $incomplete_result;    
            
            $s1 = $sql ;
            $s2 = $sql ;
            
            //echo $sql_totalLead;
        //---------------for payment status-------------------- //
            $s1 .= 'GROUP BY proposal.status';

            // echo $s1;

            $payment_result = $this->$dbType->query($s1)->result_array();  
            $data['payment_result'] = $payment_result ;
            
            $s2 .= "GROUP BY proposal.status,proposal.policy_detail_id" ;
            $payment_result_by_policy = $this->$dbType->query($s2)->result_array();  
            $data['payment_result_by_policy'] = $payment_result_by_policy ;
        //---------------for policy_detail_id -------------------- //
            $sql .= ' GROUP BY proposal.policy_detail_id';
            // echo $sql."\n";
            $result = $this->$dbType->query($sql)->result_array(); 
            $data['result'] = $result;

        // print_r($result);exit;
        //------------------------ Total Lead -----------------------------//
        //echo $sql_totalLead ;die();
        if(!empty($by_lead)){
            $getlead= " AND employee_details.lead_id = '$by_lead'";
        }else{
            $getlead="";
        }
        
        $sql_totalLead="SELECT COUNT(DISTINCT employee_details.emp_id) AS total_lead from employee_details WHERE employee_details.created_at BETWEEN '".date("Y-m-d H:i:s",strtotime($start_date))."' AND '".date("Y-m-d H:i:s",strtotime($end_date))."' AND employee_details.product_id = '".$by_product."' $getlead ";

        // echo $sql_totalLead ;        

        $result_totalLead = $this->$dbType->query($sql_totalLead)->result_array(); 

        $data['total_lead'] = $result_totalLead ;
        //------------------------ Coi -----------------------------//
        $result_coi_faliure = $this->$dbType->query($sql_coi_faliure)->result_array(); 
        $data['result_coi_faliure'] = $result_coi_faliure ;
        $result_coi_faliure_total = $this->$dbType->query($sql_coi_faliure_total)->result_array(); 
        $data['result_coi_faliure_total'] = $result_coi_faliure_total ;
        
        $result_coi_success = $this->$dbType->query($sql_coi_success)->result_array(); 
        $data['result_coi_success'] = $result_coi_success ;
        //echo $sql_coi_success_total;
        $result_coi_success_total = $this->$dbType->query($sql_coi_success_total)->result_array(); 
        $data['result_coi_success_total'] = $result_coi_success_total ;
        
        
            
    //print_pre($data);
        }
        if($_POST['export_recon-applogs']==1){
            //print_pre($_POST);die();
            $this->get_grid_excel($_POST['product_type'],$_POST['product_cover_type'],$_POST['recondates'],$_POST['time_to'],$_POST['time_from'],$_POST['lead_id'],$_POST['view_button']);
        }
        
        $this->load->employee_template('recon_report_view',$data);
        
    }
    public function get_log_data(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        set_time_limit(0);
        ini_set('memory_limit', '20000M');
        $lead_id = $_POST['l_id'] ;
        $p_cover = $_POST['p_cover'] ;
        $product_name = $_POST['product_name'] ;
        
        if($p_cover == 'GHI'){
            $a = "full_quote_request1_1" ;
            $b = "full_quote_request2_1" ;
            $c = "failure_reason_1" ;
        }else if($p_cover == 'GPA'){
            
            $a = "full_quote_request1_2" ;
            $b = "full_quote_request2_2" ;
            $c = "failure_reason_2" ;
        }else if($p_cover == 'GCI'){
            
            $a = "full_quote_request1_3" ;
            $b = "full_quote_request2_3" ;
            $c = "failure_reason_3" ;
            
        }else if($p_cover == 'GP'){
            
            $a = "full_quote_request1_8" ;
            $b = "full_quote_request2_8" ;
            $c = "failure_reason_8" ;
            
        } 
        
        $dbType = "db";
        if(!empty($product_name)){
            
                
            if($product_name == 'R05' || $product_name == 'ABC')
            {
                $dbType = "db2";
            }
        }
        $cond = ("('".$a."','".$b."','".$c."')");
        $result_sql = $this->$dbType->query("select e.lead_id,e.req,e.res,e.type,e.created_at as policy_type from logs_docs as e 
        where e.type in ".$cond." and e.lead_id = '".$lead_id."' AND ( res LIKE '%$p_cover%') group by e.type order by e.id desc limit 1")->result_array();
        //print_r($result_sql);exit;
        echo json_encode($result_sql);
        exit;
        
        
    }
    public function get_res_data(){
        
        $lead_id = $_POST['l_id'] ;
        $p_cover = $_POST['p_cover'] ;
        $product_name = $_POST['product_name'] ;
        
        if($p_cover == 'GHI'){
            $a = "full_quote_request1_1" ;
            $b = "full_quote_request2_1" ;
            $c = "failure_reason_1" ;
        }else if($p_cover == 'GPA'){
            
            $a = "full_quote_request1_2" ;
            $b = "full_quote_request2_2" ;
            $c = "failure_reason_2" ;
        }else if($p_cover == 'GCI'){
            
            $a = "full_quote_request1_3" ;
            $b = "full_quote_request2_3" ;
            $c = "failure_reason_3" ;
            
        }else if($p_cover == 'GP'){
            
            $a = "full_quote_request1_8" ;
            $b = "full_quote_request2_8" ;
            $c = "failure_reason_8" ;
            
        }
        $cond = ("('".$a."','".$b."','".$c."')");
        $dbType = "db";
        if(!empty($product_name)){
            
                
            if($product_name == 'R05' || $product_name == 'ABC')
            {
                $dbType = "db2";
            }
        }
        
        $result_sql = $this->$dbType->query("select e.lead_id,e.req,e.res,e.type,e.created_at as policy_type from logs_docs as e 
        where e.type in ".$cond." and e.lead_id = '".$lead_id."' AND ( res LIKE '%$p_cover%') group by e.type order by e.id desc limit 1")->result_array();
        //print_r($result_sql);exit;
        echo json_encode($result_sql);
        exit;
        
        
    }
    public function ajax($id){
       $states = $this->db->where("country_id",$id)->get("states")->result();
       echo json_encode($states);
    }

    public function reqs_recon_log()
     {      
             // print_r($_POST);
             // exit();

            // Database Selection Condition
            extract($this->input->post(null, true)); 

            $dbType = "db";
            if($product_type == 'R05' || $product_type == 'ABC')
            {
                $dbType = "db2";
            }

            // if ($status == 'COI Success') {
            //    $status = 'Success';
            // }

            // $statusCon  = "AND p.status ='$status'";

            if($cover == 'GHI')
            {
             $sqlquery = "SELECT e.lead_id,e.req,e.res,e.type,e.created_at,'GHI'as policy_type 
             from logs_docs as e,
             employee_details as ed,
             proposal as p,
             employee_policy_detail as epd,
             product_master_with_subtype as mpst
             where mpst.id = epd.product_name 
             AND epd.policy_detail_id = p.policy_detail_id
             and e.lead_id = ed.lead_id 
             and ed.emp_id = p.emp_id 
             and e.type in('full_quote_request1','full_quote_request2') 
             /*and JSON_EXTRACT(e.req,'$**.MasterPolicyNumber') like '[61-20-00054-00-00]%' */
             and JSON_EXTRACT(e.req,'$**.Member_Customer_ID') like '[%GHI%]' 
             and e.lead_id=".$leadid." GROUP BY e.`type` ORDER BY e.id desc LIMIT 2";
            }

            else if ($cover == 'GPA') 
            {
             $sqlquery =  "SELECT e.lead_id,e.req,e.res,e.type,e.created_at,'GPA' as policy_type 
             from logs_docs as e,
             employee_details as ed,
             proposal as p,
             employee_policy_detail as epd,
             product_master_with_subtype as mpst
             where mpst.id = epd.product_name 
             AND epd.policy_detail_id = p.policy_detail_id
             and e.lead_id = ed.lead_id 
             and ed.emp_id = p.emp_id 
             and e.type in ('full_quote_request1','full_quote_request2') 
             and JSON_EXTRACT(e.req,'$**.Member_Customer_ID') like '[%GPA%]' 
             and e.lead_id=".$leadid." GROUP BY e.`type` ORDER BY e.id desc LIMIT 2";
            }

            else
            {
             $sqlquery =  "SELECT e.lead_id,e.req,e.res,e.type,e.created_at,'GCI' as policy_type 
             from logs_docs as e,
             employee_details as ed,
             proposal as p,
             employee_policy_detail as epd,
             product_master_with_subtype as mpst
             where mpst.id = epd.product_name 
             AND epd.policy_detail_id = p.policy_detail_id
             and e.lead_id = ed.lead_id 
             and ed.emp_id = p.emp_id  
             and e.type in ('full_quote_request1','full_quote_request2') 
             and JSON_EXTRACT(e.req,'$**.Member_Customer_ID') like '[%GCI%]'  
             and e.lead_id=".$leadid." GROUP BY e.`type` ORDER BY e.id desc LIMIT 2";
            }
            
            // print $sqlquery;
            // exit();

            // $sqlquery = "select * from logs_docs where lead_id=".$leadid;

            $querystatus = $this->$dbType->query($sqlquery);

            $data['result_status'] = $querystatus->result_array();

            if(!empty($data['result_status'])){
                $output .= '<table>';

                foreach ($data['result_status'] as $key => $value) {
                    $output .= '<tr><td>Type</td><td>'.$value['type'].'</td></tr>
                                <tr><td>Request</td><td><div style="overflow-wrap: anywhere;">'.$value['req'].'</div></td></tr>';
                }
             
                $output .='</table>';
            }
            else{
                $output.='<b><div style="color: red; font-size:135%;">No records found</div></b>';
            }

            echo $output;
            // exit();
           
     }


     public function ress_recon_log()
     {
           // print_r($_POST);
           // exit();

             extract($this->input->post(null, true));
           // Database Selection Condition
            $dbType = "db";
            if($product_type == 'R05' || $product_type == 'ABC')
            {
                $dbType = "db2";
            }
            

            // if ($status == 'COI Success') {
            //    $status = 'Success';
            // }

            // $statusCon  = "AND p.status ='$status'";

            if($cover == 'GHI')
            {
             $sqlquery = "SELECT e.lead_id,e.req,e.res,e.type,e.created_at,'GHI'as policy_type 
             from logs_docs as e,
             employee_details as ed,
             proposal as p,
             employee_policy_detail as epd,
             product_master_with_subtype as mpst
             where mpst.id = epd.product_name 
             AND epd.policy_detail_id = p.policy_detail_id
             and e.lead_id = ed.lead_id 
             and ed.emp_id = p.emp_id 
             and e.type in('full_quote_request1','full_quote_request2') 
             /*and JSON_EXTRACT(e.req,'$**.MasterPolicyNumber') like '[61-20-00054-00-00]%' */
             and JSON_EXTRACT(e.req,'$**.Member_Customer_ID') like '[%GHI%]' 
             and e.lead_id=".$leadid." GROUP BY e.`type` ORDER BY e.id desc LIMIT 2";
            }

            else if ($cover == 'GPA') 
            {
             $sqlquery =  "SELECT e.lead_id,e.req,e.res,e.type,e.created_at,'GPA' as policy_type 
             from logs_docs as e,
             employee_details as ed,
             proposal as p,
             employee_policy_detail as epd,
             product_master_with_subtype as mpst
             where mpst.id = epd.product_name 
             AND epd.policy_detail_id = p.policy_detail_id
             and e.lead_id = ed.lead_id 
             and ed.emp_id = p.emp_id 
             and e.type in ('full_quote_request1','full_quote_request2') 
             and JSON_EXTRACT(e.req,'$**.Member_Customer_ID') like '[%GPA%]' 
             and e.lead_id=".$leadid." GROUP BY e.`type` ORDER BY e.id desc LIMIT 2";
            }

            else
            {
             $sqlquery =  "SELECT e.lead_id,e.req,e.res,e.type,e.created_at,'GCI' as policy_type 
             from logs_docs as e,
             employee_details as ed,
             proposal as p,
             employee_policy_detail as epd,
             product_master_with_subtype as mpst
             where mpst.id = epd.product_name 
             AND epd.policy_detail_id = p.policy_detail_id
             and e.lead_id = ed.lead_id 
             and ed.emp_id = p.emp_id  
             and e.type in ('full_quote_request1','full_quote_request2') 
             and JSON_EXTRACT(e.req,'$**.Member_Customer_ID') like '[%GCI%]'  
             and e.lead_id=".$leadid." GROUP BY e.`type` ORDER BY e.id desc LIMIT 2";
            }
            
            // print $sqlquery;
            // exit();

            // $sqlquery = "select * from logs_docs where lead_id=".$leadid;

            $querystatus = $this->$dbType->query($sqlquery);

            $data['result_status'] = $querystatus->result_array();

            if(!empty($data['result_status'])){
                $output .= '<table>';

                foreach ($data['result_status'] as $key => $value) {
                    $output .= '<tr><td>Type</td><td>'.$value['type'].'</td></tr>
                                <tr><td>Response</td><td><div style="overflow-wrap: anywhere;">'.$value['res'].'</div></td></tr>';
                }
             
                $output .='</table>';
            }
            else{
          $output.='<b><div style="color: red; font-size:135%;">No records found</div></b>';
            }

            echo $output;
            // exit();
     }


function req_log($id='')
 {
    $axisdb=$this->load->database('axis_retail',true);
    $sqlquery= "select * from logs_docs where id=".$id;
    $querystatus=$axisdb->query($sqlquery);
    $data['result_status']=$querystatus->result_array();
    
echo json_encode($data);
exit();
 }

    // Recon Report Create xlsx Export
    public function generateXls_Recon($aData) 
        {
            // create file name
            error_reporting(-1);
            ini_set('display_errors', 1);         
            
   $fileName = 'Recon-Report-'.time().'.xls';  
            // load excel library
            $this->load->library('excel');
            $listInfo = $aData;
            $objPHPExcel = new PHPExcel();      
            $objPHPExcel->setActiveSheetIndex(0);
            
            $objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(  PHPExcel_Style_NumberFormat::FORMAT_TEXT );
        
            // set Header
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Lead id');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Cover');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Payment Status');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Lead Creation Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Payment Date'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Member Id'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Start Date'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'End Date'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'COI Number'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Duplicate COI'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Failure Reason'); 
           //$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Last Request Log'); 
           //$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Last Response Log'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Lead Details'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'API Type'); 
            $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'API Count'); 
            // set Row
            $rowCount = 2;



            foreach ($listInfo as $list) 
            {
                //echo $list['lead_id'] ; exit;
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $list['lead_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $list['product_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list['status']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list['created_at']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list['created_at']);
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $list['emp_id']);




                // $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $list['start_date']);
                // $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $list['end_date']);
                if(!empty($list['start_date'])&&!empty($list['end_date'])){
                    $nstartdate = DateTime::createFromFormat('m/d/Y h:i:s A', $list['start_date'])->format('d-m-Y');
                    $nenddate = DateTime::createFromFormat('m/d/Y h:i:s A', $list['end_date'])->format('d-m-Y');    
                }else{
                    $nstartdate ='';
                    $nenddate ='';     
                }

                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $nstartdate);
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $nenddate);

                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $list['certificate_number']);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $list['coi_duplicate_empids']);
                //$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, 'N/A');
                //$objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,'N/A');
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount,$list['arr_error_log']);
                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount,'N/A');
                $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,$list['create_policy_type']);
                $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount,$list['api_count']);
                $rowCount++;
            }   
            header('Content-Type: application/vnd.ms-excel'); 
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0'); 
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
            $objWriter->save('php://output');
            exit();
        }


    // create xlsx
    public function generateXls_D2CAppLogs($aData) 
    {
        // create file name
        $fileName = 'd2c-applog-'.time().'.xls';  
        // load excel library
        $this->load->library('excel');
        $listInfo = $aData;
        $objPHPExcel = new PHPExcel();      
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(  PHPExcel_Style_NumberFormat::FORMAT_TEXT );
    
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Lead id');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'type');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Request');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Response'); 
        // set Row
        $rowCount = 2;
        foreach ($listInfo as $list) {
            //$objPHPExcel->getActiveSheet()->getStyle('A1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $list->lead_id);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $list->created_at);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list->type);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list->req);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list->res);
            $rowCount++;
        }   
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
        $objWriter->save('php://output');exit();
    }
}

//SELECT e.policy_detail_id,b.product_code,b.plan_code FROM product_master_with_subtype AS b JOIN employee_policy_detail AS e ON e.product_name = b.id WHERE e.policy_detail_id = 455 

?>