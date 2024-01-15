<?php

/**
 * Dump from SI
 *
 * @author Ankita Badak <ankita.badak@fyntune.com>
 */
class Telesalesrenewal extends CI_Controller {


        const FTPSERVER =  '103.144.217.49';
        const FTPUSERNAME = 'ABHI_AXIS';//'Fyntune';
        const FTPPASSWORD = 'Ab%ax!^13r';//'Fyntune@123';

        public function __construct(){
            parent::__construct();
            $this->model('Renewal_telesales_m',"renewal",true);
        }


        public function getstart(){

            $dwnld_reg_file_name = date("dmY")."_REG_DWNLD_GRP.xls";
            $csv_path = APPPATH."resources/Telesales_Renewal_GRP/".date('dmY')."TELESALES_RENEWAL_DWNLD_GRP.csv";
            $excel_path = APPPATH."resources/Telesales_Renewal_GRP/".date('dmY')."TELESALES_RENEWAL_DWNLD_GRP.xlsx";
    
            $remote_path = "/COI/TELESALES_RENEWAL_DWNLD_GRP.xlsx";
                
            if($connectServer['status'] == 400){
                echo json_encode($connectServer);exit;
            }
    
            $connectServer = $this->connectServer();

            $connection = $connectServer['connection'];

            if($copyFile['status'] == 400){
                echo json_encode($copyFile);exit;
            }
    
            $copyFile = $this->copyFile($connection,$remote_path,$excel_path);

            if($copyFile['status'] == 400){
                    return $copyFile;
            }                

            if(file_exists($excel_path)){
                $excelToCsv = $this->excelToCsv($excel_path,$csv_path);
                if($excelToCsv['status'] == 400){
                    echo json_encode($excelToCsv);exit;//return $excelToCsv;
                }

                $csvToArray = $this->csvToArray($csv_path); 

                if($csvToArray['status'] == 400){

                    echo json_encode($csvToArray);exit;//return $csvToArray;

                }
    
                $data = $csvToArray['data'];

                // print_pre($data);exit;
                // exit;
                foreach($data as $key => $val){

                    $cdate=date('Y-m-d H:i:s');

                    $arr=[
                        'ref_id'=>$val[0],
                        'customer_name'=>$val[1],
                        'customer_email'=>$val[2],
                        'customer_contact'=>$val[3],
                        'upi_link'=>$val[4],
                        'currency'=>$val[5],
                        'amount'=>$val[6],
                        'description'=>$val[7],
                        'expiry_by'=>$val[8],
                        'partial_payment'=>$val[9],
                        'notes_charge'=>$val[10],
                        'create_at'=>$cdate
                    ];
                    
                    $this->renewal->telesales_renewal_group($val[0],$val[2],$val[3],$arr);
                }

                $return = [
                    'status'=>200,
                    'message'=>'Data Updated successfully'
                ];
                echo json_encode($return);exit;//
            }else{
                $return = [
                    'status'=>400,
                    'message'=>'File Not found on local server'
                ];
                echo json_encode($return);exit;
            }
                
        }

        public function connectServer(){

            $return = [
                'status'=>200,
                'message'=>'connectServer Success'
            ];


            $connection =  ssh2_connect(self::FTPSERVER, 22);
    
            if ($connection){
                if(!ssh2_auth_password($connection, self::FTPUSERNAME, self::FTPPASSWORD)){
    
                    $return = [
                        'status'=>400,
                        'message'=>'Authentication failed'
                    ];
    
                    return $return;
    
                }
    
            } else {
    
                $return = [
                    'status'=>400,
                    'message'=>'NOT Connected to server'
                ];
    
                return $return;
            }
    
            $return['connection'] = $connection;
    
            return $return;
        }



        public function copyFile($connection,$remote_path,$excel_path){

            $return = [
                'status'=>200,
                'message'=>'Success file download'
            ];
            // Create SFTP session
            $sftp = ssh2_sftp($connection);
    
            $sftpStream = file_get_contents('ssh2.sftp://'.intval($sftp).$remote_path);
                
            try {
    
                if (!$sftpStream) {
                    throw new Exception("Could not open remote file: $remote_path");
    
                }
               
                file_put_contents($excel_path, $sftpStream);
                               
            } catch (Exception $e) {
                error_log('Exception: ' . $e->getMessage());
                //fclose($sftpStream);
                $return = [
                    'status'=> 400,
                    'message'=> $e->getMessage()
                ];
            }  

           // chmod($excel_path,0777);
    
            return $return;
        }
            


        public function excelTocsv($excel_path,$csv_path){

            $return = [
                'status'=>200,
                'message'=>'Success excelTocsv',
            ];
    
            try {
    
                
                include_once(APPPATH   . 'third_party/PHPExcel.php');
    
                $inputFileName = $excel_path;
    
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcelReader = $objReader->load($inputFileName);
    
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelReader, 'CSV');
                $objWriter->save($csv_path);
    
                chmod($csv_path,0777);
                
            } catch (Exception $e) {
    
                $return = [
                    'status'=>400,
                    'message'=>'fail excelTocsv'
                ];
                
            }
    
            return $return;
        }
    
    
        public function csvToArray($filename='', $delimiter=','){
    
            $header = NULL;
    
            $data = [];
    
            $return = [
                'status'=>200,
                'message'=>'Success csvToArray',
                'data'=>$data
            ];
    
            if(!file_exists($filename) || !is_readable($filename)){
    
                $return = [
                    'status'=>400,
                    'message'=>'file_exists or is_readable issue',
                    'data'=>$data
                ];
    
                return $return;
            }


            $data = array();

            if (($handle = fopen($filename, 'r')) !== FALSE){

                $i = 1;    

                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                {
                    if($row[0]=='Reference Id'){

                    }else{
                        $data[]=$row;
                    }
                    $i++;
                }   
                
                fclose($handle);
                $return['data'] = $data; 
            }

            //echo $count;
            //print_pre($data);exit;

            if(empty($data)){
    
                $return = [
                    'status'=>400,
                    'message'=>'No data in csv',
                    'data'=>$data
                ];

            }
            // print_pre($data);
            // exit;

            return $return;

        }
        

        

}

?>