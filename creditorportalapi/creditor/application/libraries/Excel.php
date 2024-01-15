<?php
// @@Author : Vinod Kumar Sharma
class Excel {

    private $excel;

    public function __construct() {
        // initialise the reference to the codeigniter instance
        require_once APPPATH.'third_party/PHPExcel.php';
        $this->excel = new PHPExcel();    
    }

    public function load($path) {
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $this->excel = $objReader->load($path);
    }

    public function save($path) {
        // Write out as the new file
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save($path);
    }

    public function stream($filename) {       
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=\"".$filename."\""); 
        header("Cache-control: private");        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');    
    }

    public function  __call($name, $arguments) {  
        // make sure our child object has this method  
        if(method_exists($this->excel, $name)) {  
            // forward the call to our child object  
            return call_user_func_array(array($this->excel, $name), $arguments);  
        }  
        return null;  
    } 

	public function exportExcel($main_table = array(),$module,$join_tables = array(), $condition = null, $sort_by = null, $limit = null, $group_by = null,$sql_query=null){
		
		//print_r($main_table);
		
		//print_r($module);
		
		
		//die('1111');
		
		
		$ci =& get_instance();
		$ci -> db ->_protect_identifiers=false;
                if($sql_query==null){
		$columns = isset($main_table[1]) ? $main_table[1] : array();
		$main_table = $main_table[0];
		
		$ci -> db -> from("$main_table");
		$join_str = "";
		foreach ($join_tables as $join_table){
			$ci -> db -> join("".$join_table[1]."", "".$join_table[2]."", "".$join_table[0]."");		
			if(isset($join_table[3])){
				$columns = array_merge($columns,$join_table[3]);
			}
		}
		
		$columns = (sizeof($columns) > 0) ? implode(", ", $columns) : "*";
		
		$ci -> db -> select("$columns");
		
		if(is_null($condition) || $condition==""){
			$condition = '1=1';
		}
		
		//$ci -> db -> where($condition);
		
		$query = $ci -> db -> get();
		$result = $query->result();
                }else{
                   $query = $ci -> db -> query($sql_query);
                    $result = $query->result(); 
                }
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Hcraft');
		
		if(!empty($result)){
			$i=1;
                            $j = 'A';
                            foreach($result[0] as $property => $value){
                                $this->excel->getActiveSheet()->setCellValue("$j$i", "$property");
					$j++;
                            }
                        $i=2;
			foreach($result as $key =>$row){
				$j = 'A';
				foreach($row as $property => $value){
					$this->excel->getActiveSheet()->setCellValue("$j$i", "$value");
					$j++;
				}
				$i++;
			}
		}
		$this->stream($module.".xls");
		//exit;
	}
	
}

?>
