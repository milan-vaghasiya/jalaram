<?php
class Dashboard extends MY_Controller{
	
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard ".$this->session->userdata("user_ip");
		$this->data['headData']->controller = "dashboard";
		$this->load->model('MachineLogModel','machineLog');
	}
	
	public function index(){
	    $times = array();
		$this->data['widgetPermission'] = $this->dashboard->getDashboardPermissions();
		$widget_class = (!empty($this->data['widgetPermission']->widget_class)) ? explode(',',$this->data['widgetPermission']->widget_class) : [];
	
		//Sales Inv
		$this->data['tdSales'] = (in_array('TSA',$widget_class)) ? $this->dashboard->getTodaySales(['to_date'=>date('Y-m-d')]) : [];
		$this->data['cmSales'] = (in_array('TSA',$widget_class)) ? $this->dashboard->getTodaySales(['from_date'=>date('Y-m-1'),'to_date'=>date('Y-m-d')]) : [];
		
		//Enquiry
		$this->data['pSeCount'] = (in_array('PSE',$widget_class)) ? $this->dashboard->getPendingSQCount(['entry_type'=>1]) : [];
	
		//Quotation
		$this->data['cmSQ'] = (in_array('PSE',$widget_class)) ? $this->dashboard->getPendingSQCount(['entry_type'=>2 ,'from_date'=>date('Y-m-1'),'to_date'=>date('Y-m-d')]) : [];
		$this->data['tdSQ'] = (in_array('PSE',$widget_class)) ? $this->dashboard->getPendingSQCount(['entry_type'=>2 ,'to_date'=>date('Y-m-d')]) : [];
	
		//Sales Order
		$this->data['unapprovedSO'] = (in_array('PSO',$widget_class)) ? $this->dashboard->getPendingSOCount(['is_approve'=>1]) : [];
		$this->data['approvedSO'] = (in_array('PSO',$widget_class)) ? $this->dashboard->getPendingSOCount() : [];
		
		//Due Sales
		$this->data['soData'] = (in_array('DS',$widget_class)) ? $this->dashboard->getDueSalesData() : [];
	    $this->data['todayBirthdayList'] = $this->employee->getEmpTodayBirthdayList();
		$reportDate = date('Y-m-d');
		$this->data['mpData'] =  (in_array('PRT',$widget_class)) ? $this->biometric->getPunchByDate($reportDate) : [];
		$presentEmp=0;$absentEmp=0;$lateEmp=0;
		if(!empty($reportDate))
		{
			if(!empty($this->data['mpData']))
			{ 
				foreach($this->data['mpData'] as $row):
					$empPunches = $row->punch_date;$status= 'P';$allPunches = "";			
					if(!empty($empPunches))
					{
						$empPunches = explode(',',$empPunches);
						$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
						$empPunches = sortDates($empPunches,$sortType);
						if(strtotime($row->shift_start) < strtotime($empPunches[0])){$lateEmp++;$status= 'P (L)';} // Count Late Arrival
						
						$ap = Array();
						foreach($empPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
						$allPunches = implode(', ',$ap);
						$status= 'P';$presentEmp++;
					}
					else{$status= 'A';$absentEmp++;}
				endforeach;	
				$this->data['presentEmp'] = $presentEmp;
				$this->data['lateEmp'] = $lateEmp;
				$this->data['absentEmp'] = $absentEmp;
			}
		}
		$this->load->view('dashboard',$this->data);
	}
	
	public function ssm(){
	    $times = array();
		$logData = $this->dashboard->getMachineLog();
		$logArray=array();
		foreach($logData as $row){
			$jobData = $this->dashboard->getMachineLogJobWise(['job_no'=>$row->job_no,'device_no'=>$row->device_no]);
			$row->job_part_count = $jobData->partCount;
			$hours = floor($jobData->production_time / 3600);
			$mins = floor(($jobData->production_time - $hours*3600) / 60);
			$s = $jobData->production_time - ($hours*3600 + $mins*60);
			$mins = ($mins<10?"0".$mins:"".$mins);
			$s = ($s<10?"0".$s:"".$s); 
			$row->today_production_time = ($hours>0?$hours.":":"").$mins.":".floor($s);


			$hours = floor($jobData->xideal_time / 3600);
			$mins = floor(($jobData->xideal_time - $hours*3600) / 60);
			$s = $jobData->xideal_time - ($hours*3600 + $mins*60);
			$mins = ($mins<10?"0".$mins:"".$mins);
			$s = ($s<10?"0".$s:"".$s); 
			$row->today_idle_item = ($hours>0?$hours.":":"").$mins.":".floor($s);

			$logArray[] = $row;
		}
		$this->data['logData'] = $logArray;
		$this->load->view('ssm',$this->data);
	}

	public function ssmJobDetail($job_no,$device_no){
		$this->data['jobData']=$this->dashboard->getMachineLogListJobWise(['device_no'=>$device_no,'job_no'=>$job_no,'log_type' => 1]);
		$this->data['device_no'] = $device_no;
		$this->load->view('ssm_job_view',$this->data);
	}

	public function getProductionLogData(){
		$data = $this->input->post();
	
		$thead = '';$tbody='';
		$i=1;
		if($data['log_type'] == 0):
		    $data['log_type'] = 1;
		    $jobData=$this->dashboard->getMachineLogListJobWise($data);
		    
			$thead .= '<tr>
				<th style="width:8%;">#</th>
				<th>Entry Time</th>
				<th>Operator</th>
				<th>Job No</th>
				<th>Part</th>
				<th>Process No</th>
				<th>Part Count</th>
				<th>Rework Count</th>
				<th>Production Time</th>
				<th>Ex. Idle Time</th>
			</tr>';
			
			foreach($jobData as $row):
				$hours = floor($row->productionTime / 3600);
				$mins = floor(($row->productionTime - $hours*3600) / 60);
				$s = $row->productionTime - ($hours*3600 + $mins*60);
				$mins = ($mins<10?"0".$mins:"".$mins);
				$s = ($s<10?"0".$s:"".$s); 
				$productionTime = ($hours>0?$hours.":":"").$mins.":".floor($s);


				$hours = floor($row->xidealTime / 3600);
				$mins = floor(($row->xidealTime - $hours*3600) / 60);
				$s = $row->xidealTime - ($hours*3600 + $mins*60);
				$mins = ($mins<10?"0".$mins:"".$mins);
				$s = ($s<10?"0".$s:"".$s); 
				$idleTimeTime = ($hours>0?$hours.":":"0:").$mins.":".floor($s);
				$tbody.= '<tr>
					 <td align="center">'.$i++.'</td>
					 <td align="center">'.date('d-m-Y',strtotime($row->created_at)).'</td>
					 <td align="center">'.$row->operator_name.'</td>
					 <td align="center">'.$row->job_no.'</td>
					 <td align="center">'.$row->item_code.'</td>
					 <td align="center">'.$row->process_name.'</td>
					 <td>'.$row->partCount.'</td>
					 <td>'.$row->rework_count.'</td>
					 <td align="center">'.$productionTime.'</td>
					 <td align="center">'.$idleTimeTime.'</td>
				</tr>';
			endforeach;
		elseif($data['log_type'] == 1):
		    $result = $this->dashboard->getMachineLogs($data);
		    
		    $thead .= '<tr>
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
            </tr>';
            
            foreach($result as $row):
    	        $diff=0;
    	        $getPreviusData = $this->machineLog->getPreviusMachineLog(1,$row->id,$row->device_no);
    	        $timeFirst = (!empty($getPreviusData))?strtotime($getPreviusData->created_at):0;
                $timeSecond = strtotime($row->created_at);
                $differenceInSeconds = $timeSecond - $timeFirst;
                $diff = (!empty($differenceInSeconds))?($differenceInSeconds - round($row->spindle_on_time)):0;
                $row->part_count = 1;
				
				$h = floor($row->production_time / 3600);
                $m = floor(($row->production_time % 3600) / 60);
                $s = $row->production_time - ($h * 3600) - ($m * 60);
                $row->production_time = sprintf('%02d:%02d:%02d', $h, $m, $s);
                
                $h = floor($row->spindle_on_time / 3600);
                $m = floor(($row->spindle_on_time % 3600) / 60);
                $s = $row->spindle_on_time - ($h * 3600) - ($m * 60);
                $row->spindle_on_time = sprintf('%02d:%02d:%02d', $h, $m, $s);
				
    	        $tbody .= '<tr>
    	            <td align="center">'.$row->id.'</td>
    	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
    	            <td align="center">'.$row->device_no.'</td>
    	            <td align="center">'.$row->production_time.'</td>
    	            <td align="center">'.$row->spindle_on_time.'</td>
    	            <td align="center">'.$row->part_count.'</td>
    	            <td align="center">'.$row->job_no.'</td>
    	            <td align="center">'.$row->process_name.'</td>
    	            <td align="center">'.$row->tool_no.'</td>
    	            <td align="center">'.floatVal($row->xideal_time).'</td>
    	            <td align="center">'.$row->rw_status.'</td>
    	            <td align="center">'.$row->operator_name.'</td>
    	            <td align="center">'.$diff.'</td>
    	        </tr>';
    	   endforeach;
			
		elseif($data['log_type'] == 2):
		    
		    $result = $this->dashboard->getMachineLogs($data);
		    
		    $thead .= '<tr>
                <th style="width:8%;">#</th>
                <th>Entry Time</th>
                <th>Job No</th>
                <th>Process No</th>
                <th>Operator Code</th>
            </tr>';
            
            foreach($result as $row):
    	       $tbody .= '<tr>
    	            <td align="center">'.$i++.'</td>
    	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
    	            <td align="center">'.$row->job_no.'</td>
    	            <td align="center">'.$row->process_no.'</td>
    	            <td align="center">'.$row->created_by.'</td>
    	       </tr>';
    	   endforeach;
		    
		elseif($data['log_type'] == 3):
		    
		    $result = $this->dashboard->getMachineLogs($data);
		    
		    $thead .= '<tr>
                <th style="width:8%;">#</th>
                <th>Entry Time</th>
                <th>Job No</th>
                <th>Process No</th>
                <th>Tool No</th>
                <th>Operator Code</th>
            </tr>';
            
            foreach($result as $row):
    	       $tbody .= '<tr>
    	            <td align="center">'.$i++.'</td>
    	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
    	            <td align="center">'.$row->job_no.'</td>
    	            <td align="center">'.$row->process_no.'</td>
    	            <td align="center">'.$row->tool_no.'</td>
    	            <td align="center">'.$row->created_by.'</td>
    	       </tr>';
    	   endforeach;
		    
		else:
		    
		    $result = $this->dashboard->getMachineLogs($data);
		    
		    $thead .= '<tr>
                <th style="width:8%;">#</th>
                <th>Entry Time</th>
                <th>Ideal Time</th>
                <th>Reason No</th>
                <th>Job No</th>
                <th>Process No</th>
                <th>Tool No</th>
                <th>Operator Code</th>
            </tr>';
            
            foreach($result as $row):
    	       $tbody .= '<tr>
    	            <td align="center">'.$i++.'</td>
    	            <td align="center">'.date('d-m-Y H:i:s',strtotime($row->created_at)).'</td>
    	            <td align="center">'.$row->ideal_time.'</td>
    	            <td align="center">'.$row->reason_no.'</td>
    	            <td align="center">'.$row->job_no.'</td>
    	            <td align="center">'.$row->process_no.'</td>
    	            <td align="center">'.$row->tool_no.'</td>
    	            <td align="center">'.$row->created_by.'</td>
    	       </tr>';
    	   endforeach;
		    
		endif;
		$this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody]);
	}
}
?>