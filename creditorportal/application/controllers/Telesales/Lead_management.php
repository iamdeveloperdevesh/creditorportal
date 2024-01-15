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
        //echo "session is ";
        //print_r($this->session->userdata('telesales_session'));
        if (!$this->session->userdata('telesales_session')) {
            redirect('login');
        }


        //print_pre($_SESSION);exit;
        if ($_SESSION['telesales_session']['is_redirect_allow'] != "1") {
            redirect('login');
        }
        //echo " end ";
        //d2c_session get value in
//	  $unset_session = array('telesales_session'=>'emp_id', 'telesales_session'=>'product_code', 'telesales_session'=>'parent_id');

        $this->session->unset_userdata($unset_session);
        $telesession = $this->session->userdata('telesales_session');
        $this->agent_id = encrypt_decrypt_password($telesession['agent_id'], 'D');
        $this->admin = $telesession['is_admin'];
        $this->load->model('Telesales/Lead_management_m', 'Lead_m');
//print_pre($telesession);exit;
//	 $this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        if (!$this->input->is_ajax_request()) {
            moduleCheck();
        }

    }

    //upendra maker/checker - 30-07-2021
    public function view_lead_maker_checker_all()
    {
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];
        $this->load->telesales_template("view_lead_maker_checker_all", $data);
    }

    public function extract_excel()
    {
        $this->load->telesales_template("exctractAv_baseAgent");
    }


    public function index()
    {
        // exit;
        // print_r($this->session->userdata('telesales_session'));exit;
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];


        if (strtolower($this->uri->segment(1)) == 'tls_view_lead') {
            $data["title"] = "Lead Management";
            // $data["agents"] = $this->db->select('id,agent_name')
            //   ->from("tls_agent_mst")
            // ->get()
            // ->result_array();
            $this->load->telesales_template("lead_view", $data);
        } else {


            //	$data["agents"] = $this->db->select('id,agent_name')
            // ->from("tls_agent_mst")
            // ->get()
            // ->result_array();

            //$data['axis_process'] = $this->Lead_m->axis_process();
            //	print_r($data);exit;
            $this->load->telesales_template("create_view", $data);
        }


    }


    /* Maker Checker */


    public function update_base_agent_update()
    {

//	echo "Base Agent";
//	$base_agent_update=$this->db->select('*')->from('tls_base_agent_tbl')->get()->result_array();
//	print_pre($base_agent_update);

//	foreach($base_agent_update as $base_agent_updatee){
        //echo $base_agent_updatee['base_agent_id'];
//$this->db->set('password',encrypt_decrypt_password($base_agent_updatee['base_agent_id']))->where('base_id',$base_agent_updatee['base_id'])->update('tls_base_agent_tbl');
        //      echo $this->db->last_query();

//	}
        echo "AV ID";
        $update_av = $this->db->select('*')->where('is_region_admin', 1)->from('tls_agent_mst')->get()->result_array();
        foreach ($update_av as $update_avv) {
            $newupdatein = $update_avv['module_access_rights'] . ",46";
            $this->db->set('module_access_rights', $newupdatein)->where('id', $update_avv['id'])->update('tls_agent_mst');
            echo $this->db->last_query();
        }


        //$this->db->set('module_access_rights','23,46')->where('base_id>=0')->update('module_access_rights');
        //echo $this->db->last_query();
    }


    public function maker_checker()
    {

        $telesession = $this->session->userdata('telesales_session');
        //print_pre($telesession);
        // exit;
        $data['s_axis_process'] = $telesession['axis_process'];
        $data["title"] = "Lead Management";
        $this->load->telesales_template("maker_checker_av_view.php");

    }

    public function get_all_agents()
    {
        $output = $this->Lead_m->get_all_agents();
        echo $output;
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
            //	->where('ed.agent_name IS NOT NULL')
            ->order_by('ed.date', 'desc')
            ->get()
            ->row_array();
        //echo $this->db->last_query();
        //print_pre($data);
        return $data;

    }


    public function disposition_agent($emp_id_disposition)
    {

        $data = $this->db->select('dm.Dispositions,dm.Sub-dispositions,ed.date,ed.agent_name,ed.remarks')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('ed.emp_id', $emp_id_disposition)
            ->where('ed.type!=', 'DO')
            //->where('ed.remarks!=','')
            ->order_by('ed.date', 'desc')
            ->get()
            ->row_array();
        //echo $this->db->last_query();exit;
        return $data;

    }

    public function get_audit_trail()
    {
        /*
        $emp_id_audit = $this->input->post('emp_id',true);
        $emp_id_audit = encrypt_decrypt_password($emp_id_audit,'D');
        $data  = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
         ->where('emp_id',$emp_id_audit)
              ->group_by('dm.Dispositions')
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
        */


        //upendra - maker/checker - 30-07-2021

        //print_pre($_SESSION['telesales_session']);exit;
        //print_pre($emp_id_audit);exit;

        $emp_id_audit = $this->input->post('emp_id', true);
        $emp_id_audit = encrypt_decrypt_password($emp_id_audit, 'D');

        //print_pre($emp_id_audit);exit;

        $is_maker_checker = $this->db->select('is_makerchecker_journey')->from('employee_details')->where('emp_id', $emp_id_audit)->get()->row_array();

        //print_pre($is_maker_checker);exit;


        //if(isset($_SESSION['telesales_session']['is_maker_checker'])){
        //  $is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];

        if ($is_maker_checker['is_makerchecker_journey'] == "yes") {

            $emp_id_audit = $this->input->post('emp_id', true);
            $emp_id_audit = encrypt_decrypt_password($emp_id_audit, 'D');
            $data = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id', $emp_id_audit)
                //->group_by('dm.Dispositions')
                ->order_by('ed.id')
                ->get()
                ->result_array();
            // $lead_creation  = $this->db->select('employee_details.created_at,employee_details.agent_name')->from('employee_details')
            // ->where('emp_id',$emp_id_audit)
            // ->get()->row_array();

            $lead_creation = $this->db->select('employee_details.created_at,tls_base_agent_tbl.base_agent_name')->from('employee_details,tls_base_agent_tbl')
                ->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
                ->where('emp_id', $emp_id_audit)
                ->get()->row_array();
            //	echo $this->db->last_query();exit;
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['base_agent_name'];
            $lead_creation_merge[0]['type'] = 'DO';

            $data = array_merge($lead_creation_merge, $data);
            echo json_encode($data);

            //}

        } else {
            $emp_id_audit = $this->input->post('emp_id', true);
            $emp_id_audit = encrypt_decrypt_password($emp_id_audit, 'D');
            $data = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id', $emp_id_audit)
                //->group_by('dm.Dispositions')
                ->order_by('ed.id')
                ->get()
                ->result_array();
            $lead_creation = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
                ->where('employee_details.assigned_to = tls_agent_mst.id')
                ->where('emp_id', $emp_id_audit)
                ->get()->row_array();
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
            $data = array_merge($lead_creation_merge, $data);
            //echo $this->db->last_query();
            echo json_encode($data);
        }
    }

    public function check_lead_lost($empid)
    {
        $lead_lost_check = $this->db->select('*')->from('employee_disposition ed,disposition_master dm')
            ->where('ed.disposition_id = dm.id')
            //->where('ed.emp_id',$empid)
            ->where('ed.emp_id', $empid)
            ->order_by('ed.date', 'desc')
            ->get()
            ->result_array();
        //print_pre($lead_lost_check);exit;
        foreach ($lead_lost_check as $key => $value) {

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

        return $last_av_mapped['agent_name'];
    }


    public function get_proposal_certificate($emp_id_cert)
    {
        $lead_lost_check = $this->db->select("(GROUP_CONCAT(distinct(p.proposal_no))) as 'proposal_no',GROUP_CONCAT(api.certificate_number) as 'cert_no',api.created_date as 'policy_issuance_date'")
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

        $lead_lost_check = $this->db->select("sum(p.premium) as 'premium'")
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

        echo json_encode($response);


    }

    public function get_datatable_maker_ajax()
    {

        //print_pre($this->input->post());
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

            if ($row->product_id == 'T01') {
                $product_name_display = 'Health pro';
            } else if ($row->product_id == 'T03') {
                $product_name_display = 'Tele Health Pro Infinity';
            } else {
                $product_name_display = $row->Plan;
            }


            $attempt_connect = $this->get_attempt_connect($row->emp_id);
            $get_latest_disposition = $this->disposition($row->emp_id);
            $get_latest_disposition_agent = $this->disposition_agent($row->emp_id);

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

            $sub_array[] = $get_latest_disposition_agent['agent_name'];

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
                $sub_array[] = '<button type="button" onclick = summary_page("' . $row->product_id . '","' . encrypt_decrypt_password($row->lead_id) . '","' . $row->emp_id . '","' . $this->agent_id . '") name="status"  class="btn btn-cta">Action</button>';
                //$sub_array[]="<a href='/tele_summary?product_id=T01&leadid=dlc3b3ZGdGloZlNockpMZUduQ0Y0Zz09'>View Details</a>";
            }

            $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
            if (((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')) && $latest_disposition == "Payment pending") {
                $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';

            } else {
                $sub_array[] = '';
            }


            // $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';


            $data[] = $sub_array;
        }
        //print_pre($data);exit;
        if (empty($data)) {
            $data = 0;
        }
        //exit;
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

        $this->db->set(['makercheckerremark' => $remark, 'lead_flag' => 'regenerated', 'makerchecker' => 'maker', 'picked_do_by' => ''])->where('emp_id', $remark_emp_id)->update('employee_details');
        if (empty($remark)) {
            $res = ['status' => 0, 'message' => 'Add Remark'];
        } else {
            $res = ['status' => 1];
        }
        echo json_encode($res);
    }

    /* datatables related code start */
    public function get_datatable_ajax()
    {

        $fetch_data = $this->Lead_m->make_datatables();
        //  echo $this->db->last_query();exit;
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
            $agentName = $_SESSION['telesales_session']['agent_name'];

            if ($row->product_id == 'T01') {
                $product_name_display = 'Health pro';
            } else if ($row->product_id == 'T03') {
                $product_name_display = 'Tele Health Pro Infinity';
            } else {
                $product_name_display = $row->Plan;
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
            //echo $this->db->last_query();exit;
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
            if ((int)$net_premium == $net_premium) {

            } else {
                $net_premium = round($net_premium, 2);
            }
            $sub_array[] = ($net_premium != 0) ? $net_premium : '';

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
                $sub_array[] = (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d H:i:s", strtotime($row->txndate)) : '';
                $sub_array[] = (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '';
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

                //	$sub_array[] = $row->imd_code;
                $sub_array[] = $row->rec_manager_code;
                $sub_array[] = $row->quotation_no;
                $sub_array[] = '';
                $sub_array[] = '';
                $sub_array[] = $row->TxRefNo;


                if ($row->is_makerchecker_journey == "yes") {
                    //$row->status = 'payment link not triggered';
                    if ($row->status == 'Success') {

                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>';
                    } else {

                        if ((strtolower($row->status) == 'payment link not triggered')) {
                            $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>
<button type="button" onclick = BackToDo("' . ($row->emp_id) . '","' . encrypt_decrypt_password($row->lead_id) . '","' . base64_encode($agentName) . '") name="status"  class="btn btn-cta"
                        >Back To Do</button>';
                        } else {
                            $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>

';
                        }

                        /*  <button type="button" onclick = BackToDo("' . ($row->emp_id) . '","' . encrypt_decrypt_password($row->lead_id) . '","'.base64_encode($agentName).'") name="status"  class="btn btn-cta"
                          >Back To Do</button>*/

                    }

                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                } else {

                    //$sub_array[] = "testing123";
                    if (strtolower($row->status) == 'rejected') {
                        $sub_array[] = '<button type="button" disabled onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    }

                    if ($row->status == 'Success') {
                        $sub_array[] = '<button type="button" id = "' . $row->policy_no . '" onclick = call_function("' . $row->policy_no . '") class="btn btn-cta"><i class="fa fa-download"></i>Download</button>';
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>';
                    } else {
                        $sub_array[] = '';
                        /*if((strtolower($row->status) == 'payment link not triggered')){
                            $sub_array[] = '<button type="button" onclick = BackToDo("' . ($row->emp_id) . '","' . encrypt_decrypt_password($row->lead_id) . '","'.base64_encode($agentName).'") name="status"  class="btn btn-cta"
                        >Back To Do</button>';
                        }*/
                    }
                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';

                }


            } else {

                if ($row->is_makerchecker_journey == "yes") {


                    $disabled_btn = "";
                    if ($row->makerchecker == "checker") {
                        $disabled_btn = "disabled";
                    }

                    $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta" ' . $disabled_btn . '>Action</button>';

                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                } else if (strtolower($row->status) == 'proposal not created') {
                    $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Action</button>';
                    $sub_array[] = '';
                    //$sub_array[] = '';
                    $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';

                } else {
                    if ($row->status == 'Success') {

                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '",0) name="status"  class="btn btn-cta"
>View Details</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = create_proposal("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Action</button>';
                    }

                    //		$sub_array[] = '';
                    // $check_time = strtr($row->txndate, '/', '-');
                    // $check_time = date("Y-m-d H:i:s",strtotime($check_time));

                    // if($row->status == 'Success' && (strtotime(date("Y-m-d H:i:s")) - strtotime($check_time)) > 3600){


                    if ((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')) {
                        $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';
                        //$sub_array[] = '<button type="button" onclick = payment_call("' . encrypt_decrypt_password($row->lead_id) . '") name="status"  class="btn btn-cta">PAYMENT</button>';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else {
                        $sub_array[] = '';
                        //$sub_array[] = '';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    }

                    if ($row->status == 'Success') {
                        $sub_array[] = '<button type="button" onclick = call_function_sendmail("' . encrypt_decrypt_password($row->emp_id) . '") class="btn btn-cta">Re-trigger COI</button>';
                    } else {
                        $sub_array[] = '';
                    }

                    //			 $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';


                }


            }
            //$sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
            //if ($this->admin != '1') {
            //     $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
            //      }
            $data[] = $sub_array;
        }
        //print_pre($data);exit;
        //print_pre($data);
        if (empty($data)) {
            $data = 0;
        }

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

        $get_details = $this->db->select('agent_id,agent_name')->from('tls_agent_mst')
            ->where('id', $av_code)
            ->get()->row_array();

        if ($return_type == 'name') {
            return $get_details['agent_name'];
        }
        if ($return_type == 'id') {
            return $get_details['agent_id'];
        }

    }


    public function get_datatable_ajax_05082021()
    {
//	echo 'aaa1111';exit;
        $fetch_data = $this->Lead_m->make_datatables();
//	print_r($fetch_data);exit;
        $data = array();
        $i = 0;
        $status_array_display = [];

        $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

        //print_pre($status_array_display);exit;
        foreach ($fetch_data as $row) {
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

            if ($row->product_id == 'T01') {
                $product_name_display = 'Health pro';
            } else if ($row->product_id == 'T03') {
                $product_name_display = 'Tele Health Pro Infinity';
            } else {
                $product_name_display = $row->Plan;
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
            $sub_array[] = $status_array_display[strtolower($row->status)];
            // $sub_array[] = $row->modified_date;
            $sub_array[] = $row->created_at;
            $sub_array[] = $row->mob_no;
            $sub_array[] = $row->saksham_id;
            $sub_array[] = $row->policy_no;
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

                // $sub_array[] = strtoupper($row->agent_name);
                //$sub_array[] = $row->av_code;


                //upendra - maker/checker - 30-07-2021
                if ($row->is_makerchecker_journey == "yes") {
                    $sub_array[] = "";
                    $sub_array[] = "";
                    //$sub_array[] = "Maker/Checker Lead";
                } else {
                    $sub_array[] = strtoupper($row->agent_name);
                    $sub_array[] = $row->av_code;
                    //$sub_array[] = "";
                }


                $sub_array[] = $get_latest_disposition['date'];
                $sub_array[] = $row->axis_location;
                //for xl download
                $sub_array[] = $row->emp_firstname;
                $sub_array[] = (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d", strtotime($row->txndate)) : '';
                $sub_array[] = (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '';
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


                /*
                if(strtolower($row->status) == 'rejected'){
                    $sub_array[] = '<button type="button" disabled onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                }else{
                    $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                }
                */
                //upendra - maker/checker - 30-07-2021

                if ($row->is_makerchecker_journey == "yes") {
                    $sub_array[] = "";
                } else {

                    if (strtolower($row->status) == 'rejected') {
                        $sub_array[] = '<button type="button" disabled onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    } else {
                        $sub_array[] = '<button type="button" onclick = reassign_agent("' . encrypt_decrypt_password($row->emp_id) . '",this,"' . encrypt_decrypt_password($row->agent_id) . '") class="btn btn-cta">REASSIGN</button>';
                    }

                }

                if ($row->status == 'Success') {
                    $sub_array[] = '<button type="button" id = "' . $row->policy_no . '" onclick = call_function("' . $row->policy_no . '") class="btn btn-cta"><i class="fa fa-download"></i>Download</button>';
                } else {
                    $sub_array[] = '';
                }

                //upendra - maker/checker - 30-07-2021
                $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';


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


                    /*
                    if((strtolower($row->status) == 'payment pending') || (strtolower($row->status) == 'payment link not triggered')){
                        $sub_array[] = '<button type="button" onclick = retrigger_pg_link("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-cta">Send Link</button>';
                        $sub_array[] = '<button type="button" onclick = payment_call("' . encrypt_decrypt_password($row->lead_id) . '") name="status"  class="btn btn-cta">PAYMENT</button>';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    }else{
                        $sub_array[] = '';
                        $sub_array[] = '';
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    }

                    if($row->status == 'Success'){
                        $sub_array[] = '<button type="button" onclick = call_function_sendmail("' . encrypt_decrypt_password($row->emp_id) . '") class="btn btn-cta">Re-trigger COI</button>';
                    }else{
                        $sub_array[] = '';
                    }
                    */
                    //upendra - maker/checker - 30-07-2021
                    $is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];

                    if ($is_maker_checker == "yes") {
                        $sub_array[] = '<button type="button" onclick = get_audit_trail("' . encrypt_decrypt_password($row->emp_id) . '") name="status"  class="btn btn-primary">Audit</button>';
                    } else {

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


            }

            $data[] = $sub_array;
        }
        //print_pre($data);exit;
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Lead_m->get_all_data(),
            "recordsFiltered" => $this->Lead_m->get_filtered_data(),
            "data" => $data,
        );


        //print_pre($output);exit;
        echo json_encode($output);
    }


    /* datatables related code start */
    public function get_datatable_ajax_old()
    {

        $fetch_data = $this->Lead_m->make_datatables();
        $data = array();
        $i = 0;
        $status_array_display = [];

        $status_array_display = ['payment link not triggered' => 'Payment Link Not Triggered', 'payment pending' => 'Payment Pending', 'payment received' => 'Payment Done', 'success' => 'Policy Issued', 'rejected' => 'Lead Lapsed', 'proposal not created' => 'Proposal Pending'];

        //print_pre($status_array_display);exit;
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
                $sub_array[] = (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d", strtotime($row->txndate)) : '';
                $sub_array[] = (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '';
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
        //print_pre($data);exit;
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
        //$telesession = $this->session->userdata('telesales_session');

        //print_pre($telesession);exit;

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
            $agent_type = $this->session->userdata('telesales_session')['agent_type'];
            if ($agent_type == 1) {
                $oldPass = encrypt_decrypt_password($oldPass);
                $rowCount = $this->db->where(["id" => $this->agent_id, "password" => $oldPass])->get("tls_agent_mst")->num_rows();

                if ($rowCount > 0) {

                    $newPass = encrypt_decrypt_password($newPass);
                    $this->db->where(["id" => $this->agent_id])->update("tls_agent_mst", [
                        "password" => $newPass,
                        "password_change_date" => date('y-m-d')
                    ]);

                    echo "0";
                } else {
                    echo "1";
                }
            } else if ($agent_type == 2) {
                $oldPass = encrypt_decrypt_password($oldPass);
                $rowCount = $this->db->where(["id" => $this->agent_id, "password" => $oldPass])->get("tls_agent_mst_outbound")->num_rows();

                if ($rowCount > 0) {

                    $newPass = encrypt_decrypt_password($newPass);
                    $this->db->where(["id" => $this->agent_id])->update("tls_agent_mst_outbound", [
                        "password" => $newPass,
                        "password_change_date" => date('y-m-d')
                    ]);

                    echo "0";
                } else {
                    echo "1";
                }
            } else if ($agent_type == 3) {
                $oldPass = encrypt_decrypt_password($oldPass);
                $rowCount = $this->db->where(["id" => $this->agent_id, "password" => $oldPass])->get("tls_master_do")->num_rows();

                if ($rowCount > 0) {

                    $newPass = encrypt_decrypt_password($newPass);
                    $this->db->where(["id" => $this->agent_id])->update("tls_master_do", [
                        "password" => $newPass,
                        "password_change_date" => date('y-m-d')
                    ]);

                    echo "0";
                } else {
                    echo "1";
                }
            } else if ($agent_type == 4) {
                $oldPass = encrypt_decrypt_password($oldPass);
                $rowCount = $this->db->where(["base_id" => $this->agent_id, "password" => $oldPass])->get("tls_base_agent_tbl")->num_rows();

                if ($rowCount > 0) {

                    $newPass = encrypt_decrypt_password($newPass);
                    $this->db->where(["base_id" => $this->agent_id])->update("tls_base_agent_tbl", [
                        "password" => $newPass,
                        "password_change_date" => date('y-m-d')
                    ]);

                    echo "0";
                } else {
                    echo "1";
                }
            }

        } else {
            $this->load->telesales_template("reset_password");
        }

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

        $lob_name = $this->db->select("axis_lob")
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

        $get_old_count = $this->db->select("axis_lob")
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

    function getextractData()
    {
        $this->load->library('excel');
        $id = $this->input->get('id');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        if ($id == 1) {
            //  base_emp_id	base agent name	tl_emp_id	tl_name	UM Name	OM/RSM Name	lob	center	vendor	status	abhi_sales_manager	abhi_area_head	rec_manager_code	imd_code	base_axis_process

            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'base_emp_id');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'base agent name');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'tl_emp_id');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'tl_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'UM Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'OM/RSM Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'center');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'LOB');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', "vendor");
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', "status");
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', "abhi_sales_manager");
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', "abhi_area_head");
            $objPHPExcel->getActiveSheet()->SetCellValue('M1', "rec_manager_code");
            $objPHPExcel->getActiveSheet()->SetCellValue('N1', "imd_code");
            $objPHPExcel->getActiveSheet()->SetCellValue('O1', "base_axis_process");
            $filename = "BASEAGENT" . date("Y-m-d his") . ".xls";
            $this->db->select('*');
            $this->db->from('tls_base_agent_tbl');
            $this->db->order_by('base_id', 'DESC');
            $this->db->limit(100);
            $query = $this->db->get()->result();
            if ($this->db->affected_rows() > 0) {
                $i = 2;
                foreach ($query as $row) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $row->base_emp_id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, $row->base_agent_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, $row->tl_emp_id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $row->tl_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $row->am_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $row->om_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, $row->center);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $row->lob);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, $row->vendor);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, $row->status);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, $row->abhi_sales_manager);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, $row->abhi_area_head);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, $row->rec_manager_code);
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, $row->imd_code);
                    $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, $row->base_axis_process);
                    $i++;
                }
            }
        } else {

            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Agent Id');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Agent name');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'tl_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'tl_emp_id');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'am_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'am_emp_id');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'om_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'om_emp_id');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'av_code');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'av_name');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'status');
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'center');
            $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'LOB');
            $objPHPExcel->getActiveSheet()->SetCellValue('N1', "axis_process");
            $objPHPExcel->getActiveSheet()->SetCellValue('O1', "license_from");
            $objPHPExcel->getActiveSheet()->SetCellValue('P1', "license_to");
            $filename = "AVMASTER" . date("Y-m-d his") . ".xls";
            // echo count($query);
            $this->db->select('*');
            $this->db->from('tls_agent_mst');
            $this->db->order_by('id', 'DESC');
            $this->db->limit(100);

            $query = $this->db->get()->result();
            if ($this->db->affected_rows() > 0) {
                $i = 2;
                foreach ($query as $row) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $row->agent_id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, $row->agent_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, $row->tl_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $row->tl_emp_id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $row->am_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $row->am_emp_id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, $row->om_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $row->om_emp_id);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, $row->av_code);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, $row->av_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, $row->status);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, $row->center);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, $row->lob);
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, $row->axis_process);
                    $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, $row->license_from);
                    $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, $row->license_to);
                    $i++;
                }
            }
        }
//exit;
        ob_end_clean();


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
        header('Location:' . APPPATH . "resources/avextract/" . $filename);

        exit;
//            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//            $objWriter->save(str_replace(__FILE__,APPPATH."resources/avextract/".$filename,__FILE__));
//            $return = [
//                'status'=>200,
//                'filename' => $filename ,
//                'message'=>'Data downloaded Successfully !'
//            ];
//            echo json_encode($return);exit;
    }

    function mdtdumps()
    {
        $this->load->telesales_template("exctractMTDdumps");
    }

    function misdumps()
    {
        $this->load->telesales_template("exctractMISdumps");
    }

    function SaveMTDdumps()
    {

        $this->load->library('excel');
        $_POST['issuancedate'] = date('Y/m/01') . "-" . date('Y/m/d');;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // $agentName = 'Teleadmin1';
        $agentName = $this->db->select('agent_id')
            ->from("tls_agent_mst")
            ->where("id", $this->agent_id)
            ->get()
            ->row()->agent_id;

        //  base_emp_id	base agent name	tl_emp_id	tl_name	UM Name	OM/RSM Name	lob	center	vendor	status	abhi_sales_manager	abhi_area_head	rec_manager_code	imd_code	base_axis_process
        if ($agentName == 'Teleadmin1') {
            $array1 = array(
                "Seq No", "Ref No", "Proposer Name", "IMD Code", "Axis Process", "Proposal No", "Plan", "Premium", "Net Premium", "Status", "Lead Generation Date", "Customer Id", "Policy Number", "Policy Issuance Date", "Disposition", "Sub-Disposition", "AV Name", "AV Id", "Last Modified Date", "Axis Location", "Payment Received Date", "Policy Issue Date", "Sum Insured", "Deductible Opted", "TL Name", "AM/UM Name", "OM/RSM Name", "LOB", "Axis Vendor", "Autorenewal", "Chronic", "BASE CALLER NAME", "BASE CALLER ID", "SM code", "Application No", "Cancellation Status", "Cancellation Date", "Transaction id", "Short URL"
            );
            $cnt1 = 1;
            $char1 = 'A';
            foreach ($array1 as $a1) {
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
                $char1++;
            }


        } else if ($agentName == 'Teleadmin2') {
            $array1 = array(
                "Seq No", "Ref No", "Proposer Name", "IMD Code", "Axis Process", "Proposal No", "Plan", "Premium", "Net Premium", "Status", "Lead Generation Date", "Mobile", "Customer Id", "Policy Number", "Policy Issuance Date", "Attempt", "Connect", "Disposition", "Sub-Disposition", "REMARK", "AV Name", "AV Id", "Last Modified Date", "Axis Location", "Payment Received Date", "Policy Issue Date", "Sum Insured", "Deductible Opted", "DOB Of Proposer", "TL Name", "AM/UM Name", "OM/RSM Name", "LOB", "Axis Vendor", "State", "City", "Pincode", "Autorenewal", "Chronic", "EMAIL", "BASE CALLER NAME", "BASE CALLER ID", "PREFERRED CONTACT DATE", "PREFERRED CONTACT TIME", "SM code", "Application No", "Cancellation Status", "Cancellation Date", "Transaction id", "Short URL"
            );
            $cnt1 = 1;
            $char1 = 'A';
            foreach ($array1 as $a1) {
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
                $char1++;
            }

        } else {
            $array1 = array(
                "Seq No", "Ref No", "Proposer Name", "Axis Process", "Proposal No", "Plan", "Premium", "Net Premium", "Status", "Lead Generation Date", "Policy Number", "Policy Issuance Date", "Disposition", "Sub-Disposition", "AV Name", "AV Id", "Last Modified Date", "Axis Location", "Payment Received Date", "Policy Issue Date", "Sum Insured", "Deductible Opted", "TL Name", "AM/UM Name", "OM/RSM Name", "LOB", "Axis Vendor", "Autorenewal", "Chronic", "BASE CALLER NAME", "BASE CALLER ID", "Application No", "Cancellation Status", "Cancellation Date", "Transaction id"
            );
            $cnt1 = 1;
            $char1 = 'A';
            foreach ($array1 as $a1) {
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
                $char1++;
            }
        }
        $code = 1;
        $fetch_data = $this->Lead_m->make_datatables();

        $filename = "MTD_data" . date("Y-m") . ".xls";
        if ($this->db->affected_rows() > 0) {
            $i = 2;

            // R06 - sms_logs_redirect_summary
            // T01 - sms_logs_redirect_summary
            // T03 - sms_logs_redirect_summary
            $m = 1;
            $cnt1 = 2;
            foreach ($fetch_data as $row) {
                $row->Autorenewal = $row->businessType;
                $row->Chronic = ($row->Chronic == 'NO') ? 'NO' : 'YES';
                $row->cancel_status = '';
                $row->cancel_date = '';
                $shortURLdata = $row->sms_logs_redirect_summary;
                //  $shortURLdata='"{\"RTdetails\":{\"PolicyID\":\"\",\"AppNo\":\"HD100017934\",\"alertID\":\"A1413\",\"channel_ID\":\"Axis Telesales\",\"Req_Id\":1,\"field1\":\"\",\"field2\":\"\",\"field3\":\"\",\"Alert_Mode\":3,\"Alertdata\":{\"mobileno\":\"8989500258\",\"emailId\":\"a@gmail.com\",\"AlertV1\":\"ABC\",\"AlertV2\":\"http:\\\/\\\/klr.pw\\\/eX1j4c\",\"AlertV3\":\"10-03-2022\",\"AlertV4\":\"1663065488\",\"AlertV5\":\"PaymentSupport.HealthInsurance@adityabirlacapital.com\",\"AlertV6\":\"\"}}}"';
                if (!is_null($shortURLdata)) {

                    $shortURLdata = trim($shortURLdata, '"');
                    $shortURLdata = (stripslashes($shortURLdata));
                    $decodedData = json_decode($shortURLdata, true);
                    $Alertdata = $decodedData['RTdetails']['Alertdata'];
                    $row->short_url = $Alertdata['AlertV2'];
                }
                $ded = $row->deductable;
                $get_latest_disposition = $this->disposition($row->emp_id);
                if ($agentName == 'Teleadmin1') {
                    $newArray = array(
                        $m, $row->lead_id, strtoupper($row->emp_firstname . ' ' . $row->last_name), $row->imd_code, $row->axis_process, $row->proposal_no, ($row->product_id == 'T01') ? 'Health pro' : $row->Plan,
                        $row->premium, $row->premium, $row->status, $row->created_at, $row->saksham_id, $row->policy_no, $row->policy_issuance_date, $get_latest_disposition['Dispositions'], $get_latest_disposition['Sub-dispositions'],
                        strtoupper($row->agent_name), $row->av_code, $get_latest_disposition['date'], $row->axis_location, (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d H:i:s", strtotime($row->txndate)) : '',
                        (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '', $row->sum_insured,
                        $ded, $row->tl_name, $row->am_name, $row->om_name, $row->axis_lob, $row->axis_vendor, $row->Autorenewal,
                        $row->Chronic, $row->base_caller_name, $row->base_caller_id, $row->rec_manager_code, $row->quotation_no,
                        $row->cancel_status, $row->cancel_date, $row->TxRefNo, $row->short_url
                    );

                    $char1 = 'A';
                    foreach ($newArray as $a2) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a2);
                        $char1++;
                    }
                } else if ($agentName == 'Teleadmin2') {
                    $attempt_connect = $this->get_attempt_connect($row->emp_id);
                    $newArray = array(
                        $m, $row->lead_id, strtoupper($row->emp_firstname . ' ' . $row->last_name), $row->imd_code, $row->axis_process, $row->proposal_no,
                        ($row->product_id == 'T01') ? 'Health pro' : $row->Plan,
                        $row->premium, $row->premium, $row->status, $row->created_at, $row->mob_no, $row->saksham_id, $row->policy_no, $row->policy_issuance_date,
                        $attempt_connect['Attempt'], $attempt_connect['Connect'],
                        $get_latest_disposition['Dispositions'], $get_latest_disposition['Sub-dispositions'], $get_latest_disposition['remarks'],
                        strtoupper($row->agent_name), $row->av_code, $get_latest_disposition['date'], $row->axis_location, (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d H:i:s", strtotime($row->txndate)) : '',
                        (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '', $row->sum_insured,
                        $ded,$row->bdate, $row->tl_name, $row->am_name, $row->om_name, $row->axis_lob, $row->axis_vendor,
                        $row->emp_state, $row->emp_city, $row->emp_pincode, $row->Autorenewal,
                        $row->Chronic, $row->email, $row->base_caller_name, $row->base_caller_id,
                        $row->preferred_contact_date, $row->preferred_contact_time, $row->rec_manager_code, $row->quotation_no,
                        $row->cancel_status, $row->cancel_date, $row->TxRefNo, $row->short_url
                    );

                    $char1 = 'A';
                    foreach ($newArray as $a2) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a2);
                        $char1++;
                    }
                } else {
                    $newArray = array(
                        $m, $row->lead_id, strtoupper($row->emp_firstname . ' ' . $row->last_name), $row->axis_process, $row->proposal_no,
                        ($row->product_id == 'T01') ? 'Health pro' : $row->Plan,
                        $row->premium, $row->premium, $row->status, $row->created_at, $row->policy_no, $row->policy_issuance_date,
                        $get_latest_disposition['Dispositions'], $get_latest_disposition['Sub-dispositions'],
                        strtoupper($row->agent_name), $row->av_code, $get_latest_disposition['date'], $row->axis_location, (!empty($row->txndate) && strtolower($row->TxStatus) == 'success') ? date("Y-m-d H:i:s", strtotime($row->txndate)) : '',
                        (!empty($row->policystartdate)) ? date("Y-m-d", strtotime($row->policystartdate)) : '', $row->sum_insured,
                        $ded, $row->tl_name, $row->am_name, $row->om_name, $row->axis_lob, $row->axis_vendor,
                        $row->Autorenewal,
                        $row->Chronic, $row->base_caller_name, $row->base_caller_id, $row->quotation_no,
                        $row->cancel_status, $row->cancel_date, $row->TxRefNo
                    );

                    $char1 = 'A';
                    foreach ($newArray as $a2) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a2);
                        $char1++;
                    }
                }
                $i++;
                $m++;
                $cnt1++;
            }
            ob_end_clean();
            header("Content-Disposition: attachment; filename=$filename");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save(str_replace(__FILE__, APPPATH . "Dumps/MTD_dumps/" . $filename, __FILE__));
            //tls_mtd_dump
            $query = $this->db->query('select * from tls_mtd_dump where month=MONTH(CURRENT_DATE()) AND year =YEAR(CURRENT_DATE())');
            if ($this->db->affected_rows() > 0) {

            } else {
                $data = array('file_name' => $filename, 'month' => date('m'), 'year' => date('Y'));
                $this->db->insert('tls_mtd_dump', $data);
            }
            $return = [
                'status' => 200,
                'filename' => $filename,
                'message' => 'Data downloaded Successfully !'
            ];
            echo json_encode($return);
            exit;
        }

    }

    public
    function executeDownload()
    {

        $filename = APPPATH . "Dumps/MTD_dumps/" . "MTD_data" . date("Y-m") . ".xls";
        ob_end_clean();
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-Length: " . filesize($filename));
        readfile($filename);
        exit;

    }

    public
    function DataMisTable()
    {
        //echo $_GET['path']; exit;
        $filename = APPPATH . base64_decode($_GET['path']);
        // $filename = APPPATH . "Dumps/MTD_dumps/" . "MTD_data" . date("Y-m") . ".xls";
        ob_end_clean();
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-Length: " . filesize($filename));
        readfile($filename);
        exit;

    }

    function SaveMISdumps()
    {

//Lob_cum_loc  LobWise_procedure  LocationwiseProc
        $this->db->query("Call Lob_cum_loc()");
        $this->db->query("Call LobWise_procedure()");
        $this->db->query("Call LocationwiseProc()");
        $this->DataMisTable1();

        $return = [
            'status' => 200,
            // 'filename' => $filename,
            'message' => 'Data downloaded Successfully !'
        ];
        echo json_encode($return);
        exit;
    }

    function DataMisTable1()
    {
        // echo 123;exit;
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 1);
        //error_reporting(E_ALL);
        $currMonth = 7;
        $curryear = 2022;
        $currDate = date("F", mktime(0, 0, 0, $currMonth, 10)) . "-" . $curryear;
        $prevDate = date("F", mktime(0, 0, 0, ($currMonth - 1), 10)) . "-" . $curryear;

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $this->getDataLocationwise($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate);
        $this->getDataLobwise($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate);
        $this->getDataLobcumLocwise($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate);
        $this->getDataLobcumLocwiseFTD($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate);
//    exit;
        ob_end_clean();
        $filename = 'MisData' . date('dmY') . ".xls";
        header("Content-Disposition: attachment; filename=$filename");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $folderName = date('mY');

        $filePath = "Dumps/MIS_dumps/" . $folderName . '/' . $filename;
        if (!file_exists(APPPATH . "Dumps/MIS_dumps/" . $folderName)) {

            mkdir(APPPATH . "Dumps/MIS_dumps/" . $folderName, 0777, true);
        }
        $objWriter->save(str_replace(__FILE__, APPPATH . "Dumps/MIS_dumps/" . $folderName . '/' . $filename, __FILE__));
        //tls_mtd_dump

        $query = $this->db->query('select * from tls_mis_dump where day=DAY(CURRENT_DATE())  and month=MONTH(CURRENT_DATE()) AND year =YEAR(CURRENT_DATE())');
        if ($this->db->affected_rows() > 0) {


        } else {
            $data = array('file_name' => $filePath, 'month' => date('m'), 'year' => date('Y'), 'day' => date('d'));
            $this->db->insert('tls_mis_dump', $data);
        }
        return true;
        /*header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="TEST123.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');*/
        //   header('Location:' . APPPATH . "resources/avextract/" . $filename);
    }

    function getDataLocationwise($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate)
    {
        $query = $this->db->query(
            "select * from tls_locationWiseData where MONTH(created_at)=" . $currMonth . " and year(created_at) = " . $curryear
        );
        if ($this->db->affected_rows() > 0) {
            $result = $query->result();
            $currentMonthData = array();
            $preMonthData = array();
            foreach ($result as $row)
                if ($currMonth == $row->month_m) {
                    $currentMonthData[$row->axis_process][$row->axis_loc][] = array($row->Net_premium, $row->NOP_count);
                } else {
                    $preMonthData[$row->axis_process][$row->axis_loc][] = array($row->Net_premium, $row->NOP_count);
                }
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', '');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', '');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:D1');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Net Premium');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:F1');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'NOP count');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Axis Process');
        $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Axis Location');
        $objPHPExcel->getActiveSheet()->SetCellValue('C2', $prevDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('D2', $currDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('E2', $prevDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('F2', $currDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Inbound Phone Banking');
        $objPHPExcel->getActiveSheet()->SetCellValue('A7', 'Outbound Call Center (OCC)');
        $arraychar = array('B', 'C', 'D', 'E', 'F');
        $array1 = array(4, 5, 6);
        $arrayData = array('Bangalore', 'Hyderabad', 'Noida');

        $array2 = array(8, 9, 10, 11, 12, 13);
        $arrayData2 = array('Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida');
        foreach ($arraychar as $key => $char) {
            foreach ($array1 as $k1 => $a1) {
                $loc = $arrayData[$k1];
                if ($char == 'B') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $loc);
                } elseif ($char == 'C') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Inbound Phone Banking'][$loc][0][0] == '' ? 0 : $preMonthData['Inbound Phone Banking'][$loc][0][0]);
                } elseif ($char == 'D') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Inbound Phone Banking'][$loc][0][0] == '' ? 0 : $currentMonthData['Inbound Phone Banking'][$loc][0][0]);
                } elseif ($char == 'E') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Inbound Phone Banking'][$loc][0][1] == '' ? 0 : $preMonthData['Inbound Phone Banking'][$loc][0][1]);
                } elseif ($char == 'F') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Inbound Phone Banking'][$loc][0][1] == '' ? 0 : $currentMonthData['Inbound Phone Banking'][$loc][0][1]);
                }

            }
            foreach ($array2 as $k2 => $a2) {
                $loc = $arrayData2[$k2];
                if ($char == 'B') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $loc);
                } elseif ($char == 'C') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $preMonthData['Outbound Call Center (OCC)'][$loc][0][0] == '' ? 0 : $preMonthData['Outbound Call Center (OCC)'][$loc][0][0]);
                } elseif ($char == 'D') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $currentMonthData['Outbound Call Center (OCC)'][$loc][0][0] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$loc][0][0]);
                } elseif ($char == 'E') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $preMonthData['Outbound Call Center (OCC)'][$loc][0][1] == '' ? 0 : $preMonthData['Outbound Call Center (OCC)'][$loc][0][1]);
                } elseif ($char == 'F') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $currentMonthData['Outbound Call Center (OCC)'][$loc][0][1] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$loc][0][1]);
                }

            }

        }


        $objPHPExcel->getActiveSheet()->SetCellValue('C3', '=SUM(C4:C6)');
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', '=SUM(D4:D6)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E3', '=SUM(E4:E6)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F3', '=SUM(F4:F6)');

        $objPHPExcel->getActiveSheet()->SetCellValue('C7', '=SUM(C8:C13)');
        $objPHPExcel->getActiveSheet()->SetCellValue('D7', '=SUM(D8:D13)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E7', '=SUM(E8:E13)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F7', '=SUM(F8:F13)');

        $objPHPExcel->getActiveSheet()->SetCellValue('A14', 'Grand Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('C14', '=SUM(C3,C7)');
        $objPHPExcel->getActiveSheet()->SetCellValue('D14', '=SUM(D3,D7)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E14', '=SUM(E3,E7)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F14', '=SUM(F3,F7)');
        return $objPHPExcel;
    }

    function getDataLobwise($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate)
    {
        $query = $this->db->query(
            "select * from tls_lobWiseData where MONTH(created_at)=" . $currMonth . " and year(created_at) = " . $curryear
        );
        $result = $query->result();

        if ($this->db->affected_rows() > 0) {
            $result = $query->result();

            $currentMonthData = array();
            $preMonthData = array();
            foreach ($result as $row)
                if ($currMonth == $row->month_m) {
                    $currentMonthData[$row->axis_process][$row->axis_lob][] = array($row->Net_premium, $row->NOP_count);
                } else {
                    $preMonthData[$row->axis_process][$row->axis_lob][] = array($row->Net_premium, $row->NOP_count);
                }
        }
        // var_dump($preMonthData['Outbound Call Center (OCC)']['OCC Burgundy'][0][0]);exit;


        $objPHPExcel->getActiveSheet()->SetCellValue('A17', '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B17', '');
        $objPHPExcel->getActiveSheet()->mergeCells('C17:D17');
        $objPHPExcel->getActiveSheet()->SetCellValue('C17', 'Net Premium');
        $objPHPExcel->getActiveSheet()->mergeCells('E17:F17');
        $objPHPExcel->getActiveSheet()->SetCellValue('E17', 'NOP count');
        $objPHPExcel->getActiveSheet()->SetCellValue('A18', 'Axis Process');
        $objPHPExcel->getActiveSheet()->SetCellValue('B18', 'Axis Location');
        $objPHPExcel->getActiveSheet()->SetCellValue('C18', $prevDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('D18', $currDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('E18', $prevDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('F18', $currDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('A19', 'Inbound Phone Banking');
        $objPHPExcel->getActiveSheet()->SetCellValue('A23', 'Outbound Call Center (OCC)');
        $arraychar = array('B', 'C', 'D', 'E', 'F');

        $array1 = array(20, 21, 22);
        $arrayData = array('Credit Cards', 'Retail Assets', 'Retail Banking');

        $array2 = array(24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35);
        $arrayData2 = array('OCC Burgundy', 'OCC CASA Domestic', 'OCC CLCM', 'OCC NRI Acq', 'OCC Priority',
            'OCC Prime', 'OCC CC Acq', 'OCC Secured Loans', 'OCC Personal Loan', 'OCC IPG', 'OCC CVM', 'OCC NDRM');
        foreach ($arraychar as $key => $char) {
            foreach ($array1 as $k1 => $a1) {
                $lob = $arrayData[$k1];
                if ($char == 'B') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $arrayData[$k1]);
                } elseif ($char == 'C') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Inbound Phone Banking'][$lob][0][0] == '' ? 0 : $preMonthData['Inbound Phone Banking'][$lob][0][0]);
                } elseif ($char == 'D') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Inbound Phone Banking'][$lob][0][0] == '' ? 0 : $currentMonthData['Inbound Phone Banking'][$lob][0][0]);
                } elseif ($char == 'E') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Inbound Phone Banking'][$lob][0][1] == '' ? 0 : $preMonthData['Inbound Phone Banking'][$lob][0][1]);
                } elseif ($char == 'F') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Inbound Phone Banking'][$lob][0][1] == '' ? 0 : $currentMonthData['Inbound Phone Banking'][$lob][0][1]);
                }

            }
            foreach ($array2 as $k2 => $a2) {


                $lob = $arrayData2[$k2];
                //echo $preMonthData['Outbound Call Center (OCC)'][$lob][0][0];exit;
                if ($char == 'B') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $lob);
                } elseif ($char == 'C') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $preMonthData['Outbound Call Center (OCC)'][$lob][0][0] == '' ? 0 : $preMonthData['Outbound Call Center (OCC)'][$lob][0][0]);
                } elseif ($char == 'D') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $currentMonthData['Outbound Call Center (OCC)'][$lob][0][0] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$lob][0][0]);
                } elseif ($char == 'E') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $preMonthData['Outbound Call Center (OCC)'][$lob][0][1] == '' ? 0 : $preMonthData['Outbound Call Center (OCC)'][$lob][0][1]);
                } elseif ($char == 'F') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a2, $currentMonthData['Outbound Call Center (OCC)'][$lob][0][1] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$lob][0][1]);
                }


            }

        }
        // exit;
        $objPHPExcel->getActiveSheet()->SetCellValue('C19', '=SUM(C20:C22)');
        $objPHPExcel->getActiveSheet()->SetCellValue('D19', '=SUM(D20:D22)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E19', '=SUM(E20:E22)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F19', '=SUM(F20:F22)');


        $objPHPExcel->getActiveSheet()->SetCellValue('C23', '=SUM(C24:C35)');
        $objPHPExcel->getActiveSheet()->SetCellValue('D23', '=SUM(D24:D35)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E23', '=SUM(E24:E35)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F23', '=SUM(F24:F35)');

        $objPHPExcel->getActiveSheet()->SetCellValue('A36', 'Grand Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('C36', '=SUM(C19,C23)');
        $objPHPExcel->getActiveSheet()->SetCellValue('D36', '=SUM(D19,D23)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E36', '=SUM(E19,E23)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F36', '=SUM(F19,F23)');
        return $objPHPExcel;
    }

    function getDataLobcumLocwise($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate)
    {
        $query = $this->db->query(
            "select * from tls_lobcumlocWiseData where MONTH(created_at)=" . $currMonth . " and year(created_at) = " . $curryear
        );
        $result = $query->result();

        if ($this->db->affected_rows() > 0) {
            $result = $query->result();

            $currentMonthData = array();
            $preMonthData = array();
            foreach ($result as $row)
                if ($currMonth == $row->month_m) {
                    $currentMonthData[$row->axis_process][$row->axis_lob][$row->axis_loc][] = array($row->Net_premium, $row->NOP_count);
                } else {
                    $preMonthData[$row->axis_process][$row->axis_lob][$row->axis_loc][] = array($row->Net_premium, $row->NOP_count);
                }
        }
        // var_dump($preMonthData['Outbound Call Center (OCC)']['OCC Burgundy'][0][0]);exit;


        $objPHPExcel->getActiveSheet()->SetCellValue('A39', '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B39', '');
        $objPHPExcel->getActiveSheet()->mergeCells('C39:D39');
        $objPHPExcel->getActiveSheet()->SetCellValue('C39', 'Net Premium');
        $objPHPExcel->getActiveSheet()->mergeCells('E39:F39');
        $objPHPExcel->getActiveSheet()->SetCellValue('E39', 'NOP count');
        $objPHPExcel->getActiveSheet()->SetCellValue('A40', 'Axis Process');
        $objPHPExcel->getActiveSheet()->SetCellValue('B40', 'LOB');
        $objPHPExcel->getActiveSheet()->SetCellValue('C40', 'Axis Location');
        $objPHPExcel->getActiveSheet()->SetCellValue('D40', $prevDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('E40', $currDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('F40', $prevDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('G40', $currDate);
        $objPHPExcel->getActiveSheet()->SetCellValue('A41', 'Inbound Phone Banking');
        $objPHPExcel->getActiveSheet()->SetCellValue('A50', 'Outbound Call Center (OCC)');


        $objPHPExcel->getActiveSheet()->SetCellValue('B42', 'Credit Cards');
        $objPHPExcel->getActiveSheet()->SetCellValue('B45', 'Retail Assets');
        $objPHPExcel->getActiveSheet()->SetCellValue('B47', 'Retail Banking');
        $outlob = array('OCC Burgundy', 'OCC CASA Domestic', 'OCC CLCM', 'OCC NRI Acq', 'OCC Priority', 'OCC Prime', 'OCC CC Acq', 'OCC Secured Loans', 'OCC Personal Loan', 'OCC IPG', 'OCC CVM', 'OCC NDRM');
        $outlobnum = array(51, 56, 63, 68, 73, 79, 86, 93, 99, 107, 113, 120);
        foreach ($outlobnum as $n => $num) {
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $num, $outlob[$n]);
        }
        $array = array(43, 44, 46, 48, 49);
        $arraychar = array('C', 'D', 'E', 'F', 'G');
        $arrayData = array('Bangalore', 'Hyderabad', 'Bangalore', 'Bangalore', 'Noida');
        $arrayDataLob = array('Credit Cards', 'Credit Cards', 'Retail Assets', 'Retail Banking', 'Retail Banking');
        $array2 = array(52, 53, 54, 55, 57, 58, 59, 60, 61, 62, 64, 65, 66, 67,
            69, 70, 71, 72, 74, 75, 76, 77, 78, 80, 81, 82, 83, 84, 85, 87, 88, 89, 90,
            91, 92, 94, 95, 96, 97, 98, 99, 101, 102, 103, 104, 105, 106, 108, 109, 110,
            111, 112, 114, 115, 116, 117, 118, 119, 121, 122, 123, 124, 125, 126);
        //
        $arrayData2 = array('Bangalore', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Bangalore', 'Kolkata', 'Mumbai', 'Noida', 'Bangalore', 'Hyderabad', 'Mumbai', 'Noida', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida', 'Ahmedabad', 'Bangalore', 'Hyderabad', 'Kolkata', 'Mumbai', 'Noida');

        $arrayDataLob2 = array('OCC Burgundy', 'OCC Burgundy', 'OCC Burgundy', 'OCC Burgundy', 'OCC CASA Domestic', 'OCC CASA Domestic', 'OCC CASA Domestic', 'OCC CASA Domestic', 'OCC CASA Domestic', 'OCC CASA Domestic', 'OCC CLCM', 'OCC CLCM', 'OCC CLCM', 'OCC CLCM', 'OCC NRI Acq', 'OCC NRI Acq', 'OCC NRI Acq', 'OCC NRI Acq', 'OCC Priority', 'OCC Priority', 'OCC Priority', 'OCC Priority', 'OCC Priority', 'OCC Prime', 'OCC Prime', 'OCC Prime', 'OCC Prime', 'OCC Prime', 'OCC Prime', 'OCC CC Acq', 'OCC CC Acq', 'OCC CC Acq', 'OCC CC Acq', 'OCC CC Acq', 'OCC CC Acq', 'OCC Secured Loans', 'OCC Secured Loans', 'OCC Secured Loans', 'OCC Secured Loans', 'OCC Secured Loans', 'OCC Secured Loans', 'OCC Personal Loan', 'OCC Personal Loan', 'OCC Personal Loan', 'OCC Personal Loan', 'OCC Personal Loan', 'OCC Personal Loan', 'OCC IPG', 'OCC IPG', 'OCC IPG', 'OCC IPG', 'OCC IPG', 'OCC CVM', 'OCC CVM', 'OCC CVM', 'OCC CVM', 'OCC CVM', 'OCC CVM', 'OCC NDRM', 'OCC NDRM', 'OCC NDRM', 'OCC NDRM', 'OCC NDRM', 'OCC NDRM');
//var_dump($preMonthData);exit;
        foreach ($arraychar as $key => $char) {
            foreach ($array as $k1 => $a1) {
                $loc = $arrayData[$k1];
                $lob = $arrayDataLob[$k1];
                if ($char == 'C') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $arrayData[$k1]);
                } elseif ($char == 'D') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Inbound Phone Banking'][$lob][$loc][0][0] == '' ? 0 : $preMonthData['Inbound Phone Banking'][$lob][$loc][0][0]);
                } elseif ($char == 'E') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Inbound Phone Banking'][$lob][$loc][0][0] == '' ? 0 : $currentMonthData['Inbound Phone Banking'][$lob][$loc][0][0]);
                } elseif ($char == 'F') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Inbound Phone Banking'][$lob][$loc][0][1] == '' ? 0 : $preMonthData['Inbound Phone Banking'][$lob][$loc][0][1]);
                } elseif ($char == 'G') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Inbound Phone Banking'][$lob][$loc][0][1] == '' ? 0 : $currentMonthData['Inbound Phone Banking'][$lob][$loc][0][1]);
                }

            }
            foreach ($array2 as $k1 => $a1) {
                $loc = $arrayData2[$k1];
                $lob = $arrayDataLob2[$k1];
                if ($char == 'C') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $arrayData2[$k1]);
                } elseif ($char == 'D') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][0] == '' ? 0 : $preMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][0]);
                } elseif ($char == 'E') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][0] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][0]);
                } elseif ($char == 'F') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $preMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][1] == '' ? 0 : $preMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][1]);
                } elseif ($char == 'G') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $a1, $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][1] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][1]);
                }

            }
        }

        $objPHPExcel->getActiveSheet()->SetCellValue('D42', '=SUM(D43:D44)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E42', '=SUM(E43:E44)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F42', '=SUM(F43:F44)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G42', '=SUM(G43:G44)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D45', '=SUM(D46:D46)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E45', '=SUM(E46:E46)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F45', '=SUM(F46:F46)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G45', '=SUM(G46:G46)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D47', '=SUM(D48:D49)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E47', '=SUM(E48:E49)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F47', '=SUM(F48:F49)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G47', '=SUM(G48:G49)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D41', '=SUM(D42,D45,D47)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E41', '=SUM(E42,E45,E47)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F41', '=SUM(E42,E45,E47)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G41', '=SUM(E42,E45,E47)');


        $objPHPExcel->getActiveSheet()->SetCellValue('D51', '=SUM(D52:D55)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E51', '=SUM(E52:E55)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F51', '=SUM(F52:F55)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G51', '=SUM(G52:G55)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D56', '=SUM(D57:D62)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E56', '=SUM(E57:E62)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F56', '=SUM(F57:F62)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G56', '=SUM(G57:G62)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D63', '=SUM(D64:D67)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E63', '=SUM(E64:E67)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F63', '=SUM(F64:F67)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G63', '=SUM(G64:G67)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D68', '=SUM(D69:D72)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E68', '=SUM(E69:E72)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F68', '=SUM(F69:F72)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G68', '=SUM(G69:G72)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D73', '=SUM(D74:D78)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E73', '=SUM(E74:E78)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F73', '=SUM(F74:F78)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G73', '=SUM(G74:G78)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D79', '=SUM(D80:D92)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E79', '=SUM(E80:E92)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F79', '=SUM(F80:F92)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G79', '=SUM(G80:G92)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D93', '=SUM(D94:D98)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E93', '=SUM(E94:E98)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F93', '=SUM(F94:F98)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G93', '=SUM(G94:G98)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D99', '=SUM(D100:D106)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E99', '=SUM(E100:E106)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F99', '=SUM(F100:F106)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G99', '=SUM(G100:G106)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D107', '=SUM(D108:D112)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E107', '=SUM(E108:E112)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F107', '=SUM(F108:F112)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G107', '=SUM(G108:G112)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D107', '=SUM(D108:D112)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E107', '=SUM(E108:E112)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F107', '=SUM(F108:F112)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G107', '=SUM(G108:G112)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D113', '=SUM(D114:D119)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E113', '=SUM(E114:E119)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F113', '=SUM(F114:F119)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G113', '=SUM(G114:G119)');

        $objPHPExcel->getActiveSheet()->SetCellValue('D120', '=SUM(D121:D126)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E120', '=SUM(E121:E126)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F120', '=SUM(F121:F126)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G120', '=SUM(G121:G126)');


        $objPHPExcel->getActiveSheet()->SetCellValue('A36', 'Grand Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('D127', '=SUM(D42,D45,D47,D51,D56,D63,D68,D73,D79,D86,D93,D99,D107,D113,D120)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E127', '=SUM(E42,E45,E47,E51,E56,E63,E68,E73,E79,E86,E93,E99,E107,E113,E120)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F127', '=SUM(F42,F45,F47,F51,F56,F63,F68,F73,F79,F86,F93,F99,F107,F113,F120)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G127', '=SUM(G42,G45,G47,G51,G56,G63,G68,G73,G79,G86,G93,G99,G107,G113,G120)');
        return $objPHPExcel;
    }

    function getDataLobcumLocwiseFTD($objPHPExcel, $currMonth, $curryear, $currDate, $prevDate)
    {
        $query = $this->db->query(
            "select * from tls_lobcumlocWiseData where MONTH(created_at)=" . $currMonth . " and year(created_at) = " . $curryear
        );
        if ($this->db->affected_rows() > 0) {
            $result = $query->result();
            $currentMonthData = array();
            $preMonthData = array();
            foreach ($result as $row) {
                if ($currMonth == $row->month_m) {
                    $currentMonthData[$row->axis_process][$row->axis_lob][$row->axis_loc][] = array($row->Net_premium, $row->NOP_count);
                }

            }

        }
//        var_dump($currentMonthData);
//        exit;

        $objPHPExcel->getActiveSheet()->mergeCells('C130:H130');
        $objPHPExcel->getActiveSheet()->mergeCells('I130:N130');
        $objPHPExcel->getActiveSheet()->mergeCells('O130:P130');
        $objPHPExcel->getActiveSheet()->SetCellValue('C130', 'Net Premium');
        $objPHPExcel->getActiveSheet()->SetCellValue('I130', 'NOP Count');
        $objPHPExcel->getActiveSheet()->SetCellValue('O130', 'Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('A131', 'Axis Process');
        $objPHPExcel->getActiveSheet()->SetCellValue('A132', 'Outbound Call Center (OCC)');
        $objPHPExcel->getActiveSheet()->SetCellValue('A145', 'Grand Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('O131', 'Net Premium');
        $objPHPExcel->getActiveSheet()->SetCellValue('P131', 'NOP');
        $array_col = array("B", "C", "D", "E", "F", "G", "H");
        $array_col2 = array("I", "J", "K", "L", "M", "N");
        $array_lob = array('OCC Burgundy', 'OCC CASA Domestic', 'OCC CLCM', 'OCC NRI Acq', 'OCC Priority', 'OCC Prime', 'OCC CC Acq', 'OCC Secured Loans', 'OCC Personal Loan', 'OCC IPG', 'OCC CVM', 'OCC NDRM');
        $array_loc = array("", "Ahmedabad", "Bangalore", "Hyderabad", "Kolkata", "Mumbai", "Noida");
        $array_locN = array("Ahmedabad", "Bangalore", "Hyderabad", "Kolkata", "Mumbai", "Noida");
        $array_pos = range(133, 144);
        foreach ($array_col as $key => $col) {

            foreach ($array_pos as $num => $pos) {
                $lob = $array_lob[$num];
                $loc = $array_locN[$key];
                if ($col == 'B') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($col . $pos, $array_lob[$num]);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue($col . $pos, $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][0] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][0]);
                }
            }
            if ($col == 'B') {
                $objPHPExcel->getActiveSheet()->SetCellValue('B131', 'LOB');
            } else {
                $sum = $col . '133:' . $col . '144';
                $objPHPExcel->getActiveSheet()->SetCellValue($col . '131', $array_loc[$key]);
                $objPHPExcel->getActiveSheet()->SetCellValue($col . '132', "=SUM(" . $sum . ")");
                $objPHPExcel->getActiveSheet()->SetCellValue($col . '145', "=SUM(" . $sum . ")");

            }
        }
        //$array_pos=range(133,160);
        foreach ($array_col2 as $key => $col2) {
            $sum = $col2 . '133:' . $col2 . '144';
            $objPHPExcel->getActiveSheet()->SetCellValue($col2 . '131', $array_locN[$key]);
            $objPHPExcel->getActiveSheet()->SetCellValue($col2 . '132', "=SUM(" . $sum . ")");
            $objPHPExcel->getActiveSheet()->SetCellValue($col2 . '145', "=SUM(" . $sum . ")");
            foreach ($array_pos as $num => $pos) {
                $lob = $array_lob[$num];
                $loc = $array_locN[$key];
                $objPHPExcel->getActiveSheet()->SetCellValue($col2 . $pos, $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][1] == '' ? 0 : $currentMonthData['Outbound Call Center (OCC)'][$lob][$loc][0][1]);
            }
        }
        $array_pos = range(132, 145);

        foreach ($array_pos as $r) {
            $sum1 = 'C' . $r . ":" . 'H' . $r;
            $sum2 = 'I' . $r . ":" . 'N' . $r;

            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $r, "=SUM(" . $sum1 . ")");
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $r, "=SUM(" . $sum2 . ")");
        }
        // $objPHPExcel->getActiveSheet()->SetCellValue('F14', '=SUM(F3,F7)');

        return $objPHPExcel;
    }

    public function get_datatable_MIS_ajax()
    {


        $this->db->select('*');//tls_mis_dump
        $this->db->from('tls_mis_dump');
        $this->db->order_by('id', 'DESC');
        $fetch_data = $this->db->get()->result();
        //   print_r($fetch_data);exit;

        $data = array();
        $i = 0;
        foreach ($fetch_data as $row) {
            $sub_array = array();
            $i++;
            $sub_array[] = $i;
            $sub_array[] = $row->day . '-' . $row->month . '-' . $row->year;
            $sub_array[] = base64_encode($row->file_name);
            $data[] = $sub_array;
        }
        //print_pre($data);exit;
        if (empty($data)) {
            $data = 0;
        }
        //exit;
        //print_pre($data);exit;
        $output = array(
            "draw" => 1,
            "recordsTotal" => count($fetch_data),
            "recordsFiltered" => count($fetch_data),
            "data" => $data,
        );


        //print_pre($output);exit;
        echo json_encode($output);
    }
}
