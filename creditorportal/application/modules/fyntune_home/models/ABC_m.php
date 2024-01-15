<?PHP
class ABC_m extends CI_Model
{ 

    function __construct(){
        parent::__construct();
        $this->load->model('Logs_m', 'Logs_m');
        $this->emp_id=1;
    }

    public function generateOtpAttemptCheck($emp_id){
        $otpVerificationData = $this->db->query("SELECT * from otp_verification where emp_id = ".$emp_id)->row_array();
        if(!empty($otpVerificationData)){
            // $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+1 hour'));
            $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+10 minutes'));
            if($otpVerificationData['submit_otp_count'] >= 5 && (date('Y-m-d H:i:s') < $timestamp)){
                $res = ["status"=>0,"message" => "Maximum attempts limit reached, try generating OTP after sometime !!"];
            } else {
                if((date('Y-m-d H:i:s') >= $timestamp) && $otpVerificationData['submit_otp_count'] >= 5){
                    $updateInvalidCount = $this->db->query("UPDATE otp_verification SET submit_otp_count = 0 where emp_id = ".$emp_id);
                }
                $res = ["status"=>1,"message" => "OTP generated !!"];
            }
        } else {
            $res = ["status"=>1,"message" => "Generating OTP for 1st time !!"];
        }
        return $res;
    }
    
    public function validCheckOtpAttempt($emp_id){
        $otpVerificationData = $this->db->query("SELECT * from otp_verification where emp_id = ".$emp_id)->row_array();
        $timestamp = date('Y-m-d H:i:s', strtotime($otpVerificationData['last_attempted_at'].'+10 minutes'));
        if($otpVerificationData['submit_otp_count'] >= 5 && (date('Y-m-d H:i:s') < $timestamp)) {
            $res = ["status"=>0,"message" => "Maximum attempts limit reached, generate new OTP after an hour !!"];
        } else {
            $res = ["status"=>1,"message" => "OTP verified successfully !!"];
        }
        if($otpVerificationData['submit_otp_count'] < 5){
            $updateInvalidCount = $this->db->query("UPDATE otp_verification SET submit_otp_count = submit_otp_count + 1 where emp_id = ".$emp_id);
        }
        return $res;
    }


    public function insertOrUpdateUserActivity($type){
        $empId = $this->emp_id;
        $this->db->where("emp_id",$empId);
        $check = $this->db->get("user_activity_abc")->result_array();

        if(empty($check)){
            $this->db->insert('user_activity_abc',['emp_id'=>$empId,'type'=>$type,'updated_time'=>date("Y-m-d H:i:s")]);
        } else {
            $this->db->where("emp_id",$empId);
            $this->db->update("user_activity_abc",['type' => $type,'updated_time'=>date("Y-m-d H:i:s")]);
        }
    }

}

?>