<?php 

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

header('Content-Type:application/json');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

class MachineLogs extends CI_Controller{
    public function __construct(){
        parent::__construct();   
        $this->load->model('masterModel');
        $this->load->model('MachineLogModel','machineLog');
        $this->load->model('ItemModel','item');
    }
    
    public function printJson($data){
		print json_encode($data);exit;
	}
	
	public function productionLog(){
	    $data = $this->input->post();
	    $data['log_type'] = 1;
	    $data['production_time'] = round(($data['production_time']/10),2);
	    $data['spindle_on_time'] = round(($data['spindle_on_time']/10),2);
	    $data['xideal_time'] = round(($data['xideal_time']/10),2);
	    $this->printJson($this->machineLog->save($data));
	}
	
	public function jobCahnge(){
	    $data = $this->input->post();
	    $data['log_type'] = 2;
	    $this->printJson($this->machineLog->save($data));
	}
	
	public function toolCahnge(){
	    $data = $this->input->post();
	    $data['log_type'] = 3;
	    $this->printJson($this->machineLog->save($data));
	}
	
	public function idealTime(){
	    $data = $this->input->post();
	    $data['log_type'] = 4;
	    $this->printJson($this->machineLog->save($data));
	}
	
	public function productionLogList($device_no=""){
	    header('Content-Type:text/html');
	    $result = Array();//$this->machineLog->getMachineLogs(1,$device_no);
	        $html='<h1>Under Development</h1>';
	    if(!empty($result))
	    {
	    
    	    $html = '<html>
    	            <head>
                        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
                    </head>
                    <body style="margin:2%;" align="center"> 
                        <h2>Production Logs</h2> 
                        <table id="productionLog" class="table table-striped table-bordered" style="width:100%;">
                           
        	            <thead>
        	                <tr>
        	                    <th style="width:8%;">#</th>
        	                    <th>Entry Time</th>
        	                    <th>Device No.</th>
        	                    <th>Production Time</th>
        	                    <th>Spindle ON Time</th>
        	                    <th>Part Count</th>
        	                    <th>Job No</th>
        	                    <th>Process No</th>
        	                    <th>Tool No</th>
        	                    <th>Ex. Ideal Time</th>
        	                    <th>Rework Status</th>
        	                    <th>Operator Code</th>
        	                    <th>L/U. Time</th>
        	                </tr>
        	            </thead>
        	            <tbody>';
    	            
        	   foreach($result as $row):
        	        $diff=0;
        	        $getPreviusData = $this->machineLog->getPreviusMachineLog(1,$row->id,$row->device_no);
        	        $timeFirst = (!empty($getPreviusData))?strtotime($getPreviusData->created_at):0;
                    $timeSecond = strtotime($row->created_at);
                    $differenceInSeconds = $timeSecond - $timeFirst;
                    $diff = (!empty($differenceInSeconds))?($differenceInSeconds - round($row->spindle_on_time)):0;
        	       
        	       $html .= '<tr>
        	            <td align="center">'.$row->id.'</td>
        	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
        	            <td align="center">'.$row->device_no.'</td>
        	            <td align="center">'.floatVal($row->production_time).'</td>
        	            <td align="center">'.floatVal($row->spindle_on_time).'</td>
        	            <td align="center">'.$row->part_count.'</td>
        	            <td align="center">'.$row->job_no.'</td>
        	            <td align="center">'.$row->process_no.'</td>
        	            <td align="center">'.$row->tool_no.'</td>
        	            <td align="center">'.floatVal($row->xideal_time).'</td>
        	            <td align="center">'.$row->rw_status.'</td>
        	            <td align="center">'.$row->created_by.'</td>
        	            <td align="center">'.$diff.'</td>
        	       </tr>';
        	   endforeach;
        	   
    	   $html .= '</tbody>
    	        </table>
    	       </body>
    	       <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    	       <script>
    	       $(document).ready(function () { $("#productionLog").DataTable({ "order": [[ 0, "desc" ]] }); });
    	       </script>
    	   </html>';
	   }
	   echo $html;exit;
	}
	
	public function jobCahngeList(){
	    header('Content-Type:text/html');
	    $result = $this->machineLog->getMachineLogs(2);
	    
	    $html = '<html>
	            <head>
                    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
                    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
                </head>
                <body style="margin:2%;" align="center"> 
                <h2>Job Change Logs</h2> 
                <table id="jobChangeLog" class="table table-striped table-bordered" style="width:100%;">
                   
	            <thead>
	                <tr>
	                    <th style="width:8%;">#</th>
	                    <th>Entry Time</th>
	                    <th>Device No.</th>
	                    <th>Job No</th>
	                    <th>Process No</th>
	                    <th>Operator Code</th>
	                </tr>
	            </thead><tbody>';
	            
	   foreach($result as $row):
	       $html .= '<tr>
	            <td align="center">'.$row->id.'</td>
	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
	            <td align="center">'.$row->device_no.'</td>
	            <td align="center">'.$row->job_no.'</td>
	            <td align="center">'.$row->process_no.'</td>
	            <td align="center">'.$row->created_by.'</td>
	       </tr>';
	   endforeach;
	   
	   $html .= '</tbody>
	        </table>
	       </body>
	       <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
	       <script>
	       $(document).ready(function () { $("#jobChangeLog").DataTable({ "order": [[ 0, "desc" ]] }); });
	       </script>
	   </html>';
	   
	   echo $html;exit;
	}
	
	public function toolCahngeList(){
	    header('Content-Type:text/html');
	    $result = $this->machineLog->getMachineLogs(3);
	    
	    $html = '<html>
	            <head>
                    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
                    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
                </head>
                <body style="margin:2%;" align="center"> 
                <h2>Tool Change Logs</h2> 
                <table id="toolChangeLog" class="table table-striped table-bordered" style="width:100%;">
                   
	            <thead>
	                <tr>
	                    <th style="width:8%;">#</th>
	                    <th>Entry Time</th>
	                    <th>Device No.</th>
	                    <th>Job No</th>
	                    <th>Process No</th>
	                    <th>Tool No</th>
	                    <th>Operator Code</th>
	                </tr>
	            </thead><tbody>';
	            
	   foreach($result as $row):
	       $html .= '<tr>
	            <td align="center">'.$row->id.'</td>
	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
	            <td align="center">'.$row->device_no.'</td>
	            <td align="center">'.$row->job_no.'</td>
	            <td align="center">'.$row->process_no.'</td>
	            <td align="center">'.$row->tool_no.'</td>
	            <td align="center">'.$row->created_by.'</td>
	       </tr>';
	   endforeach;
	   
	   $html .= '</tbody>
	        </table>
	       </body>
	       <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
	       <script>
	       $(document).ready(function () { $("#toolChangeLog").DataTable({ "order": [[ 0, "desc" ]] }); });
	       </script>
	   </html>';
	   
	   echo $html;exit;
	}
	
	public function idealTimeList(){
	    header('Content-Type:text/html');
	    $result = $this->machineLog->getMachineLogs(4);
	    
	    $html = '<html>
	            <head>
                    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
                    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
                </head>
                <body style="margin:2%;" align="center"> 
                <h2>Ideal Time Logs</h2> 
                <table id="idealTimeLog" class="table table-striped table-bordered" style="width:100%;">
                   
	            <thead>
	                <tr>
	                    <th style="width:8%;">#</th>
	                    <th>Entry Time</th>
	                    <th>Device No.</th>
	                    <th>Ideal Time</th>
	                    <th>Reason No</th>
	                    <th>Job No</th>
	                    <th>Process No</th>
	                    <th>Tool No</th>
	                    <th>Operator Code</th>
	                </tr>
	            </thead><tbody>';
	            
	   foreach($result as $row):
	       $html .= '<tr>
	            <td align="center">'.$row->id.'</td>
	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
	            <td align="center">'.$row->device_no.'</td>
	            <td align="center">'.$row->ideal_time.'</td>
	            <td align="center">'.$row->reason_no.'</td>
	            <td align="center">'.$row->job_no.'</td>
	            <td align="center">'.$row->process_no.'</td>
	            <td align="center">'.$row->tool_no.'</td>
	            <td align="center">'.$row->created_by.'</td>
	       </tr>';
	   endforeach;
	   
	   $html .= '</tbody>
	        </table>
	       </body>
	       <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
	       <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
	       <script>
	       $(document).ready(function () { $("#idealTimeLog").DataTable({ "order": [[ 0, "desc" ]] }); });
	       </script>
	   </html>';
	   
	   echo $html;exit;
	}
	// Get Machine MaAximum Idle Time for asking Compulsory Idle Reason
	public function getMaxIdleTimeOfMachine(){
	    $data = $this->input->get();
	    $machineData = $this->machineLog->getMaxIdleTimeOfMachine($data);
	    $this->printJson(['max_idle_time'=>$machineData->max_idle_time]);
	}
}

?>