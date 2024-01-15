<?PHP
class Login_abc_m extends CI_Model
{ 
    
    function __construct(){
        parent::__construct();
        $this->load->model('Logs_m', 'Logs_m');
    }



    public function insert_data($data)
    {
        $this->db->insert('lead_details' , $data);
        $emp_id = $this->db->insert_id();
        $logs_array['data'] = ["type" => "insert_employee_details","req" => json_encode($data), "lead_id" => $this->session->userdata('lead_id'), "mob_no" => $this->session->userdata('mob_no'),"product_id" => 'ABC'];
        $this->Logs_m->insertLogs($logs_array);
       return $emp_id;
    }

}

?>