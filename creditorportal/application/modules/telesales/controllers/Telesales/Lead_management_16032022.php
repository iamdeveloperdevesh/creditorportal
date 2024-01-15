   <?php

    if (!defined('BASEPATH')) {
        exit('No direct script access allowed');
    }
    require_once APPPATH . "controllers/MY_TelesalesSessionCheck.php";

    class Lead_management extends MY_TelesalesSessionCheck
    {
        public $agent_id;
        public $admin;
        public function __construct()
        {
            parent::__construct();

            if (!$this->session->userdata('telesales_session')) {
                redirect('login');
            }
            //d2c_session get value in
            $telesession = $this->session->userdata('telesales_session');
            $this->agent_id = encrypt_decrypt_password($telesession['agent_id'], 'D');
            $this->admin = $telesession['is_admin'];
            $this->load->model('Telesales/Lead_management_m', 'Lead_m');
            // $pw = encrypt_decrypt_password('WU1VYVp5N25HNlZETGhNVDB4QWY2dz09', 'D');
            // echo $pw;exit;

            if ($_SESSION['telesales_session']['is_redirect_allow'] != "1") {
                redirect('login');
            }
        }

        //upendra maker/checker - 30-07-2021
        public function view_lead_maker_checker_all()
        {
            $telesession = $this->session->userdata('telesales_session');
            $data['s_axis_process'] = $telesession['axis_process'];
            $this->load->telesales_template("view_lead_maker_checker_all", $data);
        }


        //dynamic lob
        public function dynamic_lob_view()
        {
            $this->load->telesales_template("dynamic_lob_view");
        }

        public function dynamic_lob_load()
        {
            $fetch_data = $this->Lead_m->dynamic_lob_load();
            // print_r($fetch_data);exit;

            $i = 1;

            foreach ($fetch_data as $row) {

                if ($row->is_maker_checker == "1") {
                    $maker_checker_html = '<h5><span class="badge badge-success ed_status" data-id="' . $row->axis_lob_id . '" data-type="maker_checker" data-status="enabled">Enabled</span></h5>';
                } else {
                    $maker_checker_html = '<h5><span class="badge badge-danger ed_status" data-id="' . $row->axis_lob_id . '" data-type="maker_checker" data-status="disabled">Disabled</span></h5>';
                }


                if ($row->telesales_journey == "1") {
                    $telesales_journey_html = '<h5><span class="badge badge-success ed_status" data-id="' . $row->axis_lob_id . '" data-type="solo_journey" data-status="enabled">Enabled</span></h5>';
                } else {
                    $telesales_journey_html = '<h5><span class="badge badge-danger ed_status" data-id="' . $row->axis_lob_id . '" data-type="solo_journey" data-status="disabled">Disabled</span></h5>';
                }

                $sub_data = [];
                $sub_data[] = $i++;
                $sub_data[] = $row->axis_lob;
                $sub_data[] = $telesales_journey_html;
                $sub_data[] = $maker_checker_html;
                $sub_data[] = $row->axis_process;
                $sub_data[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick="editLob(this.id,this.value);" id="' . $row->axis_process . '" data-toggle="modal" data-target="#myModal" value="' . $row->axis_lob . '"><i class="ti-pencil"></i></button>';
                $data[] = $sub_data;
            }

            // exit;

            $output = array(
                "recordsTotal" => $this->Lead_m->dynamic_lob_load_count(),
                "recordsFiltered" => $this->Lead_m->dynamic_lob_load_count(),
                "data" => $data,
            );

            echo json_encode($output);
        }


        public function change_status_dynamic_lob()
        {
            extract($this->input->post(null, true));

            $lob_name  = $this->db->select("axis_lob")
                ->from('tls_axis_lob')
                ->where('axis_lob_id', $id)
                ->get()
                ->row_array();


            $lob_name = $lob_name['axis_lob'];






            if ($journey_type == "maker_checker") {

                if ($data_status == "enabled") {
                    $is_maker_checker = 0;
                } else {
                    $is_maker_checker = 1;
                }

                $update_lob = $this->db->set('is_maker_checker', $is_maker_checker);
                $this->db->where('axis_lob', $lob_name);
                $this->db->update('tls_axis_lob');
            }

            if ($journey_type == "solo_journey") {

                if ($data_status == "enabled") {
                    $telesales_journey = 0;
                } else {
                    $telesales_journey = 1;
                }


                $update_lob = $this->db->set('telesales_journey', $telesales_journey);
                $this->db->where('axis_lob', $lob_name);
                $this->db->update('tls_axis_lob');
            }
        }

        public function submit_edit_dynamic_lob()
        {
            extract($this->input->post(null, true));



            $update_lob = $this->db->set('axis_lob', $lob_name);
            $this->db->set('axis_process', $ap_edit);
            $this->db->where('axis_lob', $axis_lob_old_name);
            $this->db->update('tls_axis_lob');

            // echo $this->db->last_query();exit;


            $output['status'] = "success";
            echo json_encode($output);
            exit;
        }


        public function submit_add_dynamic_lob()
        {

            extract($this->input->post(null, true));

            $get_old_count  = $this->db->select("axis_lob")
                ->from('tls_axis_lob')
                ->where('axis_lob', $add_lob_name)
                ->get()
                ->result_array();

            // echo $this->db->last_query();exit;
            if (count($get_old_count) > 0) {
                $output['status'] = "duplicate";
                echo json_encode($output);
                exit;
            } else {

                $fdata = [
                    'axis_lob' => $add_lob_name,
                    'axis_process' => $add_lob_ap,
                    'axis_vendor_id' => "1",
                    'is_maker_checker' => $add_lob_dual_journey,
                    'telesales_journey' => $add_lob_solo_journey,
                ];

                $this->db->insert('tls_axis_lob', $fdata);

                $output['status'] = "success";
                echo json_encode($output);
                exit;
            }
        }



        public function index()
        {

            $telesession = $this->session->userdata('telesales_session');

            // echo $this->Lead_m->get_all_agents();exit;

            $data['s_axis_process'] = $telesession['axis_process'];
            // $data['is_admin']=$telesession['is_admin'];
            // print_r($_SESSION);exit;

            if (strtolower($this->uri->segment(1)) == 'tls_view_lead') {

                $data["title"] = "Lead Management";

                // print_pre($telesession);exit;
                // if($telesession['is_admin'] == 1){
                //      $data["agents"] = $this->db->select('id,agent_name')
                //     ->from("tls_agent_mst")
                //     ->get()
                //     ->result_array();
                // }


                // print_pre($data);exit;
                $this->load->telesales_template("lead_view", $data);
            } else {


                // $data["agents"] = $this->db->select('id,agent_name')
                // ->from("tls_agent_mst")
                // ->get()
                // ->result_array();
                //:echo site_url();exit;


                // $data['axis_process'] = $this->Lead_m->axis_process();
                $this->load->telesales_template("create_view", $data);
            }

            // if(strtolower($this->uri->segment(1)) == 'tls_view_lead'){
            // 	$data["title"] = "Lead Management";
            // $data["agents"] = $this->db->select('id,agent_name')
            //     ->from("tls_agent_mst")
            //     ->get()
            //     ->result_array();
            // 	$this->load->telesales_template("lead_view", $data);
            // }
            // else{
            // 	$data = '';
            // 	$this->load->telesales_template("create_view", $data);
            // }



        }

        public function maker_checker()
        {

            $telesession = $this->session->userdata('telesales_session');
            // print_pre($telesession);
            // exit;        
            $data['s_axis_process'] = $telesession['axis_process'];
            $data["title"] = "Lead Management";
            $this->load->telesales_template("maker_checker_av_view", $data);
        }

        public function get_all_agents()
        {
            $output = $this->Lead_m->get_all_agents();
            echo $output;
        }


        public function maker_checker_create_password_mod()
        {

            // echo encrypt_decrypt_password('Akash');

            //Update AV access module module_access_rights

            $this->db->select('id,module_access_rights');
            $this->db->from('tls_agent_mst');
            $this->db->where('is_admin', 0);
            $this->db->where('is_region_admin', 0);
            $results = $this->db->get()->result_array();

            foreach ($results as $result) {
                $new_module_access_rights = $result['module_access_rights'] . ',42';

                echo $new_module_access_rights . "<br>";

                // $this->db->where('id',$result['id'])->update('tls_agent_mst',['module_access_rights'=>$new_module_access_rights]);

            }



            //Generate base agent password
            // $this->db->select('base_id,base_agent_id');
            // $this->db->from('tls_base_agent_tbl');
            // $result=$this->db->get()->result_array();
            // foreach($result as $results){            
            //     $this->db->where('base_id',$results['base_id'])->update('tls_base_agent_tbl',['password'=>encrypt_decrypt_password($results['base_agent_id']),'module_access_rights'=>'23,43','base_axis_process'=>'Outbound Call Center (OCC)']);            
            // }



        }


        public function get_attempt_connect($emp_id_payment)
        {

            $data = $this->db->select('sum(dm.Attempt) as Attempt,sum(dm.Connect)as Connect')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('ed.emp_id', $emp_id_payment)
                ->group_by('ed.emp_id')
                ->get()
                ->row_array();
            return $data;
        }
        public function disposition($emp_id_disposition)
        {

            $data = $this->db->select('dm.Dispositions,dm.Sub-dispositions,ed.date,ed.agent_name,ed.remarks')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('ed.emp_id', $emp_id_disposition)
                //  ->where('ed.remarks!=','')
                ->order_by('ed.date', 'desc')
                ->get()
                ->row_array();
            //echo $this->db->last_query();exit;
            return $data;
        }
        public function get_audit_trail()
        {  $emp_id_audit = $this->input->post('emp_id',true);
                $emp_id_audit = encrypt_decrypt_password($emp_id_audit,'D');


      $is_maker_checker=$this->db->select('is_makerchecker_journey')->from('employee_details')->where('emp_id',$emp_id_audit)->get()->row_array();


            if($is_maker_checker['is_makerchecker_journey'] == "yes"){

                $emp_id_audit = $this->input->post('emp_id',true);
                $emp_id_audit = encrypt_decrypt_password($emp_id_audit,'D');
                $data  = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id',$emp_id_audit)
                ->order_by('ed.id')
                ->get()
                ->result_array();


                $lead_creation  = $this->db->select('employee_details.created_at,tls_base_agent_tbl.base_agent_name')->from('employee_details,tls_base_agent_tbl')
                ->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
                ->where('emp_id',$emp_id_audit)
                ->get()->row_array();
                $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
                $lead_creation_merge[0]['disposition_id'] = 123;
                $lead_creation_merge[0]['Dispositions'] = 'LEAD';
                $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
                $lead_creation_merge[0]['agent_name'] = $lead_creation['base_agent_name'];
                $lead_creation_merge[0]['type'] = 'DO';

                $data = array_merge($lead_creation_merge,$data);
                echo json_encode($data);
			}
			else{
            $emp_id_audit = $this->input->post('emp_id',true);
            $emp_id_audit = encrypt_decrypt_password($emp_id_audit,'D');
            $data  = $this->db->select('*')
             ->from('employee_disposition ed')
             ->join('disposition_master dm', 'ed.disposition_id = dm.id')
             ->where('emp_id',$emp_id_audit)
             //->group_by('dm.Dispositions')
             ->order_by('ed.id')
            ->get()
            ->result_array();
            $lead_creation  = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
            ->where('employee_details.assigned_to = tls_agent_mst.id')
            ->where('emp_id',$emp_id_audit)
            ->get()->row_array();
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
            $data = array_merge($lead_creation_merge,$data);
            //echo $this->db->last_query();
            echo json_encode($data);
        }
        }
        public function check_lead_lost($empid)
        {
            $lead_lost_check  = $this->db->select('*')->from('employee_disposition ed,disposition_master dm')
                ->where('ed.disposition_id = dm.id')
                //->where('ed.emp_id',$empid)
                ->where('ed.emp_id', $empid)
                ->order_by('ed.date', 'desc')
                ->get()
                ->result_array();
            //print_pre($lead_lost_check);exit;
            foreach ($lead_lost_check as $key =>  $value) {

                if ($value['Dispositions'] == 'Lead Lapsed') {
                    $serach = $key + 1;
                    return $lead_lost_check[$serach]['Dispositions'];
                }
            }
            return '';
        }


        public function last_av_mapped($emp_id)
        {
            $last_av_mapped = $this->db->select('agent_name')->from('employee_dispostion')->where('emp_id', $emp_id)->order_by('id', 'DESC')->limit(1)->get()->row_array();

            return $last_av_mapped;
        }

        public function get_proposal_certificate($emp_id_cert)
        {
            //ankita dedeup junk changes added created date field in select query
            $lead_lost_check  = $this->db->select("(GROUP_CONCAT(distinct(p.proposal_no))) as 'proposal_no',GROUP_CONCAT(api.certificate_number) as 'cert_no',api.created_date as 'policy_issuance_date'")
                ->from('proposal p')
                ->join('api_proposal_response api', 'p.id = api.proposal_id', 'left')
                //->where('ed.emp_id',$empid)
                ->where('p.emp_id', $emp_id_cert)
                ->get()
                ->row_array();
            return $lead_lost_check;
        }

        public function get_total_premium($emp_id_premium)
        {

            $lead_lost_check  = $this->db->select("sum(p.premium) as 'premium'")
                ->from('proposal p')
                //->where('ed.emp_id',$empid)
                ->where('p.emp_id', $emp_id_premium)
                ->get()
                ->row_array();
            return $lead_lost_check;
        }


        /* Maker Checker */


        public function lead_picked_by()
        {
            extract($this->input->post(), true);
            $lead_id = encrypt_decrypt_password($lead_id, 'D');
            //    $this->db->set('picked_do_by',$picked_by)->where('lead_id',$lead_id)->update('employee_details');
            // echo json_encode($picked_by);
            $check_lead_picked = $this->db->select('is_makerchecker_journey,makerchecker,picked_do_by')->from('employee_details')->where('lead_id', $lead_id)->where("picked_do_by!=' '")->where('is_makerchecker_journey', 'yes')->get()->row_array();

            if ($check_lead_picked['picked_do_by'] == $picked_by) {
                $response['status'] = 'no';
            } else if ($check_lead_picked['picked_do_by'] == '') {
                $response['status'] = 'no';
                $this->db->set('picked_do_by', $picked_by)->where('lead_id', $lead_id)->update('employee_details');
            } else {
                $response['status'] = 'yes';
                //   $this->db->set('picked_do_by',$picked_by)->where('lead_id',$lead_id)->update('employee_details');
            }

            echo  json_encode($response);
        }

        public function get_datatable_maker_ajax()
        {

            // print_pre($this->input->post());
            // exit;
            $fetch_data = $this->Lead_m->makerchecker_datatables();

            // print_r($fetch_data);exit;

            $data = array();
            $i = 0;
            $status_array_display = [];

            $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

            //print_pre($status_array_display);exit;
            foreach ($fetch_data as $row) {
                // print_pre($row);exit;
                if (strtolower($row->status) == 'payment pending') {
                    if ($row->sms_trigger_status == 0) {
                        $row->status = 'payment link not triggered';
                    }
                }
                if ($row->product_id == 'T01' || $row->product_id == 'T03' || $row->product_id == 'R06') {
                    $get_proposal_certificate = $this->get_proposal_certificate($row->emp_id);
                    //print_pre($get_proposal_certificate);exit;
                    $row->proposal_no = $get_proposal_certificate['proposal_no'];
                    $row->policy_no = $get_proposal_certificate['cert_no'];
                    $row->premium = $this->get_total_premium($row->emp_id)['premium'];
                }

                // 03-02-2022 - SVK005
                if ($row->product_id == 'T01') {
                    $product_name_display = 'Health pro';
                } else if ($row->product_id == 'T03') {
                    $product_name_display = 'Health Pro Infinity';
                } else if ($row->product_id == "R06") {
                    $product_name_display = 'Group Activ Health';
                }



                $attempt_connect = $this->get_attempt_connect($row->emp_id);
                $get_latest_disposition = $this->disposition($row->emp_id);

                $sub_array = array();
                $i++;
                $sub_array[] = $i;
                $sub_array[] = $row->lead_id;
                $sub_array[] = $row->proposal_no;

                $sub_array[] = strtoupper($row->emp_firstname . ' ' . $row->last_name);

                $sub_array[] = $row->created_at;
                $sub_array[] = $get_latest_disposition['date'];
                // $sub_array[] = $row->created_at;


                // $sub_array[] = $row->imd_code;            
                // $sub_array[] = $row->axis_process;            
                // $sub_array[] = $product_name_display;

                // $sub_array[] = strtoupper($row->base_caller_id);
                $sub_array[] = strtoupper($row->base_caller_name);

                $sub_array[] = $get_latest_disposition['agent_name'];

                // $sub_array[] = $get_latest_disposition['agent_name'];

                if ($row->product_id == "R06") {
                    $product_name_display = "Group Activ Health";
                }
                if ($row->product_id == "T03") {
                    $product_name_display = "Health Pro Infinity";
                }
                if ($row->product_id == "T01") {
                    $product_name_display = "Health Pro";
                }

                $sub_array[] = $product_name_display;

                $sub_array[] = $row->premium;

                $sub_array[] = $row->axis_location;
                $sub_array[] = $row->axis_lob;

                $latest_disposition = $get_latest_disposition['Dispositions'];;

                $sub_array[] = $latest_disposition;

                $sub_array[] = $get_latest_disposition['Sub-dispositions'];
                $sub_array[] = $status_array_display[strtolower($row->status)];

                $sub_array[] = $row->policy_issuance_date;

                $sub_array[] = $row->makercheckerremark;


                // $sub_array[] = $row->modified_date;
                if ($row->lead_flag == 'regenerated' || $get_latest_disposition['Dispositions'] == 'Payment done') {
                    $sub_array[] = '<button type="button"  name="status"  class="btn btn-cta" disabled>View Details</button>';
                } else {
                    $sub_array[] = '<button type="button" onclick = summary_page("' . $row->product_id . '","' . encrypt_decrypt_password($row->lead_id) . '","' . $row->emp_id . '","' . $this->agent_id . '") name="status"  class="btn btn-cta">View Details</button>';
                }

                if (((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered'))&& $latest_disposition == "Payment pending") {
                    $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';
                   
                } else {
                    $sub_array[] = '';
                }

                $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';

                

                // $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';


                $data[] = $sub_array;
            }
            //print_pre($data);exit;
            $output = array(
                "draw" => intval($_POST["draw"]),
                "recordsTotal" => $this->Lead_m->get_all_maker_data(),
                "recordsFiltered" => $this->Lead_m->get_filtered_maker_data(),
                "data" => $data,
            );


            //print_pre($output);exit;
            echo json_encode($output);
        }






        public function save_maker_checker_remark()
        {

            extract($this->input->post(), true);

            // $data=['emp_id'=>$remark_emp_id,'lead_id'=>$remark_lead,'remark'=>$remark,'do_id'=>$remark_agent_id,'do_name'=>$remark_agent_name];

            // $this->db->insert('maker_checker_audit',$data);

            if (!empty($pddisposition)) {
                $fpddisposition = $pddisposition;
            } else {
                $fpddisposition = '56';
            }
            $disposition = ['emp_id' => $remark_emp_id, 'disposition_id' => $fpddisposition, 'date' => date('Y-m-d H:i:s'), 'agent_name' => $remark_agent_name, 'remarks' => $remark, 'type' => 'AV'];
            $this->db->insert('employee_disposition', $disposition);
            $this->db->set(['makercheckerremark' => $remark, 'lead_flag' => 'regenerated', 'makerchecker' => 'maker'])->where('emp_id', $remark_emp_id)->update('employee_details');
            if (empty($remark)) {
                $res = ['status' => 0, 'message' => 'Add Remark'];
            } else {
                $res = ['status' => 1];
            }
            echo json_encode($res);
        }



        public function get_net_premium_tele_test()
        {
            // $lead_id = "1642404696";
            $net_premium = $this->Lead_m->get_net_premium_tele_test($lead_id);
            echo $net_premium;

            // $this->Lead_m->get_quotation_no("9879814654");

        }


        /* datatables related code start */
        public function get_datatable_ajax()
        {

            $fetch_data = $this->Lead_m->make_datatables();

            // print_r($fetch_data);exit;

            $data = array();
            $i = 0;
            $status_array_display = [];

            $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

            //print_pre($status_array_display);exit;
            foreach ($fetch_data as $row) {
                // print_pre($row);exit;
                if (strtolower($row->status) == 'payment pending') {
                    if ($row->sms_trigger_status == 0) {
                        $row->status = 'payment link not triggered';
                    }
                }
                if ($row->product_id == 'T01' || $row->product_id == 'T03' || $row->product_id == 'R06') {
                    $get_proposal_certificate = $this->get_proposal_certificate($row->emp_id);
                    //print_pre($get_proposal_certificate);exit;
                    $row->proposal_no = $get_proposal_certificate['proposal_no'];
                    $row->policy_no = $get_proposal_certificate['cert_no'];
                    $row->premium = $this->get_total_premium($row->emp_id)['premium'];
                }

                // 03-02-2022 - SVK005
                if ($row->product_id == 'T01') {
                    $product_name_display = 'Health pro';
                } else if ($row->product_id == 'T03') {
                    $product_name_display = 'Health Pro Infinity';
                } else if ($row->product_id == "R06") {
                    $product_name_display = 'Group Activ Health';
                }

                if ($row->is_makerchecker_journey == "yes") {
                    if ($row->product_id == "R06") {
                        $product_name_display = "Group Activ Health";
                    }
                    if ($row->product_id == "T03") {
                        $product_name_display = "Health Pro Infinity";
                    }
                    if ($row->product_id == "T01") {
                        $product_name_display = "Health Pro";
                    }
                }



                $attempt_connect = $this->get_attempt_connect($row->emp_id);
                $get_latest_disposition = $this->disposition($row->emp_id);
                $sub_array = array();
                $i++;
                $sub_array[] = $i;
                $sub_array[] = $row->lead_id;
                $sub_array[] = strtoupper($row->emp_firstname . ' ' . $row->last_name);
                $sub_array[] = $row->imd_code;
                $sub_array[] = $row->axis_process;

                $sub_array[] = $row->proposal_no;
                $sub_array[] = $product_name_display;
                $sub_array[] = $row->premium;

                $net_premium = $this->Lead_m->get_net_premium_tele($row->lead_id);
                if ((int) $net_premium == $net_premium) {
                } else {
                    $net_premium = round($net_premium, 2);
                }
                $sub_array[] = ($net_premium != 0) ?  $net_premium : '';
                $sub_array[] = $status_array_display[strtolower($row->status)];
                // $sub_array[] = $row->modified_date;
                $sub_array[] = $row->created_at;
                $sub_array[] = $row->mob_no;
                $sub_array[] = $row->saksham_id;
                $sub_array[] = $row->policy_no;
                //ankita added this filed junk dedupe logic
                $sub_array[] = $row->policy_issuance_date;
                $sub_array[] = $row->lead_id;
                $sub_array[] = $attempt_connect['Attempt'];
                $sub_array[] = $attempt_connect['Connect'];
                $sub_array[] = $get_latest_disposition['Dispositions'];
                $sub_array[] = $get_latest_disposition['Sub-dispositions'];
                $sub_array[] = $get_latest_disposition['remarks'];
                $sub_array[] = $this->check_lead_lost($row->emp_id);
                //echo $this->check_lead_lost($row->emp_id);	exit;
                if ($this->admin == '1') {

                    //upendra - maker/checker - 30-07-2021
                    if ($row->is_makerchecker_journey == "yes") {
                        $sub_array[] = $this->get_maker_checker_av($row->picked_do_by, 'name');
                        $sub_array[] = $this->get_maker_checker_av($row->picked_do_by, 'id');
                        // $sub_array[] = "Maker/Checker Lead";
                    } else {
                        $sub_array[] = strtoupper($row->agent_name);
                        $sub_array[] = $row->av_code;
                        // $sub_array[] = "";
                    }

                    // $sub_array[] = strtoupper($row->agent_name);
                    // $sub_array[] = $row->av_code;
                    // $sub_array[] = '';
                    $sub_array[] = $get_latest_disposition['date'];
                    $sub_array[] = $row->axis_location;
                    //for xl download
                    $sub_array[] = $row->emp_firstname;
                    $sub_array[] = (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ?  date("Y-m-d H:i:s", strtotime($row->txndate)) : '';
                    $sub_array[] = (!empty($row->policystartdate)) ?  date("Y-m-d", strtotime($row->policystartdate)) : '';
                    $sub_array[] = $row->sum_insured;
                    $sub_array[] = $row->bdate;
                    $sub_array[] = strtoupper($row->tl_name);
                    $sub_array[] = strtoupper($row->am_name);
                    $sub_array[] = strtoupper($row->om_name);
                    $sub_array[] = $row->axis_lob;
                    //$sub_array[] = $row->axis_location;
                    $sub_array[] = $row->axis_vendor;
                    $sub_array[] = $row->emp_state;
                    $sub_array[] = $row->emp_city;
                    $sub_array[] = $row->emp_pincode;
                    //$sub_array[] = $row->mob_no;
                    $sub_array[] = $row->businessType;
                    $sub_array[] = ($row->Chronic == 'NO') ? 'NO' : 'YES';
                    $sub_array[] = $row->email;
                    $sub_array[] = strtoupper($row->base_caller_name);
                    $sub_array[] = strtoupper($row->base_caller_id);
                    $sub_array[] = $row->preferred_contact_date;
                    $sub_array[] = $row->preferred_contact_time;



                    // $sub_array[] = $row->imd_code;
                    $sub_array[] = $row->rec_manager_code;
                    $sub_array[] = $row->quotation_no;
                    $sub_array[] = '';
                    $sub_array[] = '';
                    $sub_array[] = $row->TxRefNo;


                    if ($row->is_makerchecker_journey == "yes") {
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta"
                    >View Details</button>';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else {
                        if (strtolower($row->status) == 'rejected') {
                            $sub_array[] = '<button type="button" disabled onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                        } else {
                            $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                        }

                        if ($row->status == 'Success') {
                            $sub_array[] = '<button type="button" id = "' . $row->policy_no . '" onclick = call_function("' . $row->policy_no . '") class="btn btn-cta"><i class="fa fa-download"></i>Download</button>';
                        } else {
                            $sub_array[] = '';
                        }

                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    }
                } else {

                    if ($row->is_makerchecker_journey == "yes") {

                        $disabled_btn = "";
                        if ($row->makerchecker == "checker") {
                            $disabled_btn = "disabled";
                        }

                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta" ' . $disabled_btn . '>View Details</button>';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else if (strtolower($row->status) == 'proposal not created') {
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Action</button>';
                        $sub_array[] = '';
                        // $sub_array[] = '';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else {
                         $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">View Details</button>';
                        // $sub_array[] = '';
                        // $check_time = strtr($row->txndate, '/', '-');
                        // $check_time = date("Y-m-d H:i:s",strtotime($check_time));

                        // if($row->status == 'Success' && (strtotime(date("Y-m-d H:i:s")) - strtotime($check_time)) > 3600){



                        if ((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')) {
                            $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';
                            // $sub_array[] = '<button type="button" onclick = payment_call("' . encrypt_decrypt_password($row->lead_id) . '") name="status"  class="btn btn-cta">PAYMENT</button>';
                            // $sub_array[] = '';
                            $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                        } else {
                            $sub_array[] = '';
                            // $sub_array[] = '';

                            $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                        }

                        if ($row->status == 'Success') {
                            $sub_array[] = '<button type="button" onclick = call_function_sendmail("' . encrypt_decrypt_password($row->emp_id) . '") class="btn btn-cta">Re-trigger COI</button>';
                        } else {
                            $sub_array[] = '';
                        }
                    }
                }
                // $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                $data[] = $sub_array;
            }
            if (empty($data)) {
                $data = 0;
            }

            // print_pre($data);exit;
            $output = array(
                "draw" => intval($_POST["draw"]),
                "recordsTotal" => $this->Lead_m->get_all_data(),
                "recordsFiltered" => $this->Lead_m->get_filtered_data(),
                "data" => $data,
            );


            //print_pre($output);exit;
            echo json_encode($output);
        }

        public function get_maker_checker_av($av_code, $return_type)
        {

            $get_details  = $this->db->select('agent_id,agent_name')->from('tls_agent_mst')
                ->where('id', $av_code)
                ->get()->row_array();

            if ($return_type == 'name') {
                return $get_details['agent_name'];
            }
            if ($return_type == 'id') {
                return $get_details['agent_id'];
            }
        }

        public function get_datatable_ajax_old()
        {

            $fetch_data = $this->Lead_m->make_datatables();
            $data = array();
            $i = 0;
            $status_array_display = [];

            $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

            // print_pre($fetch_data);exit;
            foreach ($fetch_data as $row) {
                if (strtolower($row->status) == 'payment pending') {
                    if ($row->sms_trigger_status == 0) {
                        $row->status = 'payment link not triggered';
                    }
                }
                if ($row->product_id == 'T01') {
                    $get_proposal_certificate = $this->get_proposal_certificate($row->emp_id);
                    //print_pre($get_proposal_certificate);exit;
                    $row->proposal_no = $get_proposal_certificate['proposal_no'];
                    $row->policy_no = $get_proposal_certificate['cert_no'];
                    $row->premium = $this->get_total_premium($row->emp_id)['premium'];
                }
                $attempt_connect = $this->get_attempt_connect($row->emp_id);
                $get_latest_disposition = $this->disposition($row->emp_id);
                $sub_array = array();
                $i++;
                $sub_array[] = $i;
                $sub_array[] = $row->lead_id;
                $sub_array[] = strtoupper($row->emp_firstname . ' ' . $row->last_name);
                $sub_array[] = $row->imd_code;
                $sub_array[] = $row->proposal_no;
                $sub_array[] = ($row->product_id == 'T01') ? 'Health pro' : $row->Plan;
                $sub_array[] = $row->premium;
                $sub_array[] = $status_array_display[strtolower($row->status)];
                // $sub_array[] = $row->modified_date;
                $sub_array[] = $row->created_at;
                $sub_array[] = $row->mob_no;
                $sub_array[] = $row->saksham_id;
                $sub_array[] = $row->policy_no;
                $sub_array[] = $row->lead_id;
                $sub_array[] = $attempt_connect['Attempt'];
                $sub_array[] = $attempt_connect['Connect'];
                $sub_array[] = $get_latest_disposition['Dispositions'];
                $sub_array[] = $get_latest_disposition['Sub-dispositions'];
                $sub_array[] = $get_latest_disposition['remarks'];
                $sub_array[] = $this->check_lead_lost($row->emp_id);
                //echo $this->check_lead_lost($row->emp_id);	exit;
                if ($this->admin == '1') {

                    $sub_array[] = strtoupper($row->agent_name);
                    $sub_array[] = $row->av_code;
                    $sub_array[] = $get_latest_disposition['date'];
                    $sub_array[] = $row->axis_location;
                    //for xl download
                    $sub_array[] = $row->emp_firstname;
                    $sub_array[] = (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ?  date("Y-m-d", strtotime($row->txndate)) : '';
                    $sub_array[] = (!empty($row->policystartdate)) ?  date("Y-m-d", strtotime($row->policystartdate)) : '';
                    $sub_array[] = $row->sum_insured;
                    $sub_array[] = $row->bdate;
                    $sub_array[] = strtoupper($row->tl_name);
                    $sub_array[] = strtoupper($row->am_name);
                    $sub_array[] = strtoupper($row->om_name);
                    $sub_array[] = $row->axis_lob;
                    //$sub_array[] = $row->axis_location;
                    $sub_array[] = $row->axis_vendor;
                    $sub_array[] = $row->emp_state;
                    $sub_array[] = $row->emp_city;
                    $sub_array[] = $row->emp_pincode;
                    //$sub_array[] = $row->mob_no;
                    $sub_array[] = $row->businessType;
                    $sub_array[] = ($row->Chronic == 'NO') ? 'NO' : 'YES';
                    $sub_array[] = $row->email;
                    $sub_array[] = strtoupper($row->base_caller_name);
                    $sub_array[] = strtoupper($row->base_caller_id);
                    $sub_array[] = $row->preferred_contact_date;
                    $sub_array[] = $row->preferred_contact_time;





                    if (strtolower($row->status) == 'rejected') {
                        $sub_array[] = '<button type="button" disabled onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    }

                    if ($row->status == 'Success') {
                        $sub_array[] = '<button type="button" id = "' . $row->policy_no . '" onclick = call_function("' . $row->policy_no . '") class="btn btn-cta"><i class="fa fa-download"></i>Download</button>';
                    } else {
                        $sub_array[] = '';
                    }
                } else {


                    if (strtolower($row->status) == 'proposal not created') {
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Action</button>';
                        $sub_array[] = '';
                        $sub_array[] = '';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">View Details</button>';

                        // $check_time = strtr($row->txndate, '/', '-');
                        // $check_time = date("Y-m-d H:i:s",strtotime($check_time));

                        // if($row->status == 'Success' && (strtotime(date("Y-m-d H:i:s")) - strtotime($check_time)) > 3600){



                        if ((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')) {
                            $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';
                            $sub_array[] = '<button type="button" onclick = payment_call("' . encrypt_decrypt_password($row->lead_id) . '") name="status"  class="btn btn-cta">PAYMENT</button>';
                            $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                        } else {
                            $sub_array[] = '';
                            $sub_array[] = '';
                            $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                        }

                        if ($row->status == 'Success') {
                            $sub_array[] = '<button type="button" onclick = call_function_sendmail("' . encrypt_decrypt_password($row->emp_id) . '") class="btn btn-cta">Re-trigger COI</button>';
                        } else {
                            $sub_array[] = '';
                        }
                    }
                }

                $data[] = $sub_array;
            }
            // print_pre($data);exit;
            $output = array(
                "draw" => intval($_POST["draw"]),
                "recordsTotal" => $this->Lead_m->get_all_data(),
                "recordsFiltered" => $this->Lead_m->get_filtered_data(),
                "data" => $data,
            );





            //print_pre($output);exit;
            echo json_encode($output);
        }
        /* datatables related code end */

        public function insert_lead()
        {
            // $this->form_validation->set_rules('lead_id', 'lead_id', 'trim|required');
            // $this->form_validation->set_rules('saksham_id', 'saksham_id', 'trim|required');
            $this->form_validation->set_rules('salutation', 'salutation', 'trim|required');
            $this->form_validation->set_rules('first_name', 'first_name', 'trim|required');
            // $this->form_validation->set_rules('last_name', 'last_name', 'trim|required');

            //    if($this->input->post('axis_process')=='Inbound Phone Banking'){
            //     $this->form_validation->set_rules('saksham_id', 'saksham_id', 'trim|required');
            //     }


            $this->form_validation->set_rules('axis_process', 'axis_process', 'trim|required');
            $this->form_validation->set_rules('gender1', 'gender1', 'trim|required');
            $this->form_validation->set_rules('mob_no', 'mob_no', 'trim|required|integer|exact_length[10]');
            $this->form_validation->set_rules('dob', 'dob', 'trim|required');
            // $this->form_validation->set_rules('email', 'email', 'trim|required');
            $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');

            $this->form_validation->set_error_delimiters('', '');
            if ($this->form_validation->run() == false) {
                $z = validation_errors();
                $response = ["status" => false, "message" => $z];
                echo json_encode($response);
                return;
            }
            $response = $this->Lead_m->insert_lead();
            echo json_encode($response);
        }
        public function redirect_proposal()
        {
            $response['status'] = false;
            $emp_id = $this->input->post('emp_id');
            if (!empty($emp_id)) {
                $emp_id = encrypt_decrypt_password($emp_id, 'D');
                $parent_id = $this->db->select('p.policy_parent_id,l.lead_id')
                    ->from('employee_details l')
                    ->join('product_master_with_subtype p', 'l.product_id = p.product_code')
                    ->where('l.emp_id', $emp_id)
                    ->get()
                    ->row_array();
                $lead_id = encrypt_decrypt_password($parent_id['lead_id']);
                $_SESSION['telesales_session']['emp_id'] = $emp_id;
                $_SESSION['telesales_session']['parent_id'] = $parent_id['policy_parent_id'];
                $response['lead_id'] = $lead_id;
                $response['status'] = true;
            }
            echo json_encode($response);
        }
        public function reassign_agent()
        {
            $response['status'] = false;
            $response['message'] = 'Something Went Wrong';
            $emp_id = $this->input->post('emp_id', true);
            $agent_id = $this->input->post('agent_id', true);
            if (!empty($emp_id) && !empty($agent_id)) {
                $this->db->where('emp_id', encrypt_decrypt_password($emp_id, 'D'));
                $this->db->update('employee_details', ["assigned_to" => encrypt_decrypt_password($agent_id, 'D')]);
                $response['agent_name'] = $this->db->select("agent_name")->from("tls_agent_mst")->where("id", encrypt_decrypt_password($agent_id, 'D'))->get()->row_array();
                $response['status'] = true;
                $response['message'] = 'Agent Reassigned Successfully';
            }
            echo json_encode($response);
        }
        public function resetPassword()
        {
            if (@$this->input->post()) {
                extract($this->input->post(null, true));

                $oldPass = encrypt_decrypt_password($oldPass);
                $rowCount = $this->db->where(["id" => $this->agent_id, "password" => $oldPass])->get("tls_agent_mst")->num_rows();

                if ($rowCount > 0) {

                    $newPass = encrypt_decrypt_password($newPass);
                    $this->db->where(["id" => $this->agent_id])->update("tls_agent_mst", [
                        "password" => $newPass,
                    ]);

                    echo "0";
                } else {
                    echo "1";
                }
            } else {
                $this->load->telesales_template("reset_password");
            }
        }
    }
