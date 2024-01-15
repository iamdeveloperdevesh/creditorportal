<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
* Class Name : Logs_m 
* @package : Affinity
* @subpackage : Manage Logs Details in logs_post_data & logs_docs
* @Description :  
* @author : Shardul Kulkarni <shardul.kulkarni@fyntune.com>
*/
class Logs_m extends CI_Model
{
    function __construct() {
        parent::__construct();
		$this->db2 = $this->load->database('telesales_fyntune',TRUE);
    }

	/**
	* Insert Logs Details in logs_post_data & logs_docs
	* @param array $dataArray  This parameter contain Table name and data array which we want to save into the database
	* Sample Code : 
	* $dataArray['tablename'] = 'logs_post_data'; 
	* $dataArray['data'] = data; 
	* $this->Logs_m->insertLogs($dataArray);
	*/
	public function insertLogs($dataArray = array())
    {
    	$axisIds = ['ABC','MUTHOOT','HERO_FINCORP','D2C2','R05','D01','D02'];
		$tableName = !empty($dataArray['tablename']) ? $dataArray['tablename'] : 'logs_post_data';
		$data	   = !empty($dataArray['data']) ? $dataArray['data']: "";
		if(!empty($data)) {
			if(isset($data['product_id']) && in_array($data['product_id'],$axisIds)){
				$this->db2->insert($tableName, $data);
				$emp_id = $this->db2->select("emp_id")->from("employee_details")->where("lead_id",$data['lead_id'])->where("product_id",$data['product_id'])->get()->row_array();
				$this->db2->where("emp_id",$emp_id['emp_id']);
				$this->db2->update("employee_details",['modified_date' => date('Y-m-d H:i:s')]);
			}else{
				$this->db->insert($tableName, $data);
			}
			
		}	
    }
}
