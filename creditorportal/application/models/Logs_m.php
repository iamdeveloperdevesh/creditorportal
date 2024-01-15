<?PHP
class Logs_m extends CI_Model
{ 



	public function insertLogs($dataArray = array())
    {
    	$axisIds = ['ABC','MUTHOOT','HERO_FINCORP','D2C2','R05','D01','D02'];
		$tableName = !empty($dataArray['tablename']) ? $dataArray['tablename'] : 'logs_post_data';
		$data	   = !empty($dataArray['data']) ? $dataArray['data']: "";
		if(!empty($data)) {
			if(isset($data['product_id']) && in_array($data['product_id'],$axisIds)){
				$this->db->insert($tableName, $data);
				$emp_id = $this->db->select("emp_id")->from("lead_details")->where("lead_id",$data['lead_id'])->where("product_id",$data['product_id'])->get()->row_array();
				$this->db->where("emp_id",$emp_id['emp_id']);
				$this->db->update("lead_details",['updatedon' => date('Y-m-d H:i:s')]);
			}else{
				$this->db->insert($tableName, $data);
			}
			
		}	
    }


}
?>