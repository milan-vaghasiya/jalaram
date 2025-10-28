<?php
class HrReport extends MY_Controller
{
    private $indexPage = "report/hr_report/index";
    private $emp_report = "report/hr_report/emp_report";
    private $monthlyAttendance = "report/hr_report/month_attendance";
    private $monthSummary = "report/hr_report/month_summary";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "HR Report";
		$this->data['headData']->controller = "reports/hrReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/hr_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'HR REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

	public function empReport(){
		$this->data['headData']->pageTitle = "Employee Report";
        $this->data['pageHeader'] = 'EMPLOYEE REPORT';
        $empData = $this->employee->getEmpReport();
        $i=1; $this->data['empData'] = ""; 
        foreach($empData as $row):
            $empEdu = $this->employee->getEmpEdu($row->id);
            $course = Array();
            foreach($empEdu as $edu):
                $course[] = $edu->course;
            endforeach;
            $this->data['empData'] .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->emp_code.'</td>
                <td>'.$row->title.'</td>
                <td>'.$row->emp_contact.'</td>
                <td>'.date('d-m-Y',strtotime($row->emp_birthdate)).'</td>
                <td>'.implode(', ',$course).'</td>
                <td>'.$row->emp_experience.'</td>
                <td>'.formatDate($row->emp_joining_date).'</td>
                <td>'.formatDate($row->emp_relieve_date).'</td>
            </tr>';
        endforeach;
        $this->load->view($this->emp_report,$this->data);
    }
    
    public function getEmployeeData(){
        $emp_status = $this->input->post('emp_status');
        $empData = $this->employee->getEmployeeForReport($emp_status);
        $i=1; $html = ""; 
        foreach($empData as $row):
            $empEdu = $this->employee->getEmpEdu($row->id);
            $course = Array();
            foreach($empEdu as $edu):
                $course[] = $edu->course;
            endforeach;
            $html .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->emp_code.'</td>
                <td>'.$row->title.'</td>
                <td>'.$row->emp_contact.'</td>
                <td>'.date('d-m-Y',strtotime($row->emp_birthdate)).'</td>
                <td>'.implode(', ',$course).'</td>
                <td>'.$row->emp_experience.'</td>
                <td>'.formatDate($row->emp_joining_date).'</td>
                <td>'.formatDate($row->emp_relieve_date).'</td>
            </tr>';
        endforeach;
        $this->printJson(['status'=>1,'tbody'=>$html]);
    }

    public function mismatchPunch(){        
        $this->data['pageHeader'] = 'MISMATCH PUNCH REPORT';
        $this->load->view("report/hr_report/mismatch_punch",$this->data);
    }

    public function getMismatchPunch(){
        $report_date = $this->input->post('report_date');
        $empData = $this->attendance->getMismatchPunchData($report_date);
        $html = "";
        foreach($empData as $row):
            $html .= '
                <tr>
                    <td>'.$row->emp_code.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->department_name.'</td>
                    <td>'.$row->shift_name.'</td>
                    <td>'.$row->title.'</td>
                    <td>'.$row->category.'</td>
                    <td>'.$row->punch_time.'</td>
                    <td>'.$row->missed_punch.' <a href="#" class="float-right manualAttendance" data-empid="'.$row->id.'" data-adate="'.$report_date.'" data-button="both" data-modal_id="modal-md" data-function="addManualAttendance" data-form_title="Add Manual Attendance"> Add</a></td>
                </tr>
            ';
        endforeach;
        $this->printJson(['status'=>1,'tbody'=>$html]);
    }

	public function monthlyAttendance(){
        $this->load->view($this->monthlyAttendance,$this->data);
    }

    public function loadAttendanceSheet(){
        $data = $this->input->post();
		$this->printJson($this->attendance->loadAttendanceSheet($data['month']));
    }
	
    public function printMonthlyAttendance($month,$file_type = 'excel'){
	
		$month = date('m',strtotime($month));
		set_time_limit(0);
		$empData = $this->attendance->getEmployeeList();
		$companyData = $this->attendance->getCompanyInfo();
		$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
		$FromDate = date("d/m/Y_00:01",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y_11:59",strtotime($year.'-'.$month.'-01'));
		
		
		$first_day = 1;
		$last_day = date("t",strtotime($year.'-'.$month.'-01'));
		
		$punchData = NULL;$empCount = 1;
			
		$emp1 = Array();$response = '';$empTable='';$pageData = Array();
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);
				$present = 0;$leave = 0;$absent = 0;$theadDate = '';$theadDay = '';$wo = 0;$monthWH = 0;$wh=0;$wi = 0;$oth = 0;$oti = 0;
				$inData = '';$outData = '';$lunchInData = '';$lunchOutData = '';$workHrs = '';$otData = '';$status = '';
				
				$inData .= '<tr><th style="border:1px solid #888;font-size:12px;">IN</th>';
				$lunchInData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-START</th>';
				$lunchOutData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-END</th>';
				$outData .= '<tr><th style="border:1px solid #888;font-size:12px;">OUT</th>';
				$workHrs .= '<tr><th style="border:1px solid #888;font-size:12px;">WH</th>';
				$otData .= '<tr><th style="border:1px solid #888;font-size:12px;">OT</th>';
				$status .= '<tr><th style="border:1px solid #888;font-size:12px;">STATUS</th>';
				for($d=1;$d<=$last_day;$d++)
				{
					$punchData = New StdClass();$empPucnhes = Array();
					$filterDate = date("Y-m-d",strtotime($year.'-'.$month.'-'.$d));
					$punchData = $this->biometric->getPunchData($filterDate,$filterDate);
					
					if(!empty($punchData))
					{
						$punches = json_decode($punchData[0]->punch_data);
						$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
					}
					$attend_status = false;
					$currentDate = date('d/m/Y', strtotime($year.'-'.$month.'-'.$d));$punchDates = Array();$punchTimes = Array();
					$day = date("D",strtotime($year.'-'.$month.'-'.$d));if($day == 'Wed'){$wo++;}
					$theadDate .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">'.$d.'</th>';
					$theadDay .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">'.$day.'</th>';
					
					if(!empty($empPucnhes))
					{
						foreach($empPucnhes as $punch)
						{
							$todayPunch = $punches[$punch];							
							$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
							$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
						}
					}							
					// Get Manual Punches
					$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
					if(!empty($mpData))
					{
						foreach($mpData as $mpRow):
							$time = explode(" ",$mpRow->punch_in);
							$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
							$punchTimes[] = date("H:i:s",strtotime($time[1]));
						endforeach;
					}
					
					if(!empty($punchDates))
					{
						$attend_status = true;
						$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
						$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start));
						$shiftEnd = date('d-m-Y H:i:s', strtotime('+8 hours',strtotime($shiftStart)));
						$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
						if(strtotime($punch_in) > strtotime($shiftEnd))
						{
							$shiftEnd = date('d-m-Y H:i:s', strtotime($currentDate.' 23:59:59'));
						}
						
						$late_in =  date('d-m-Y H:i:s', strtotime($shiftStart.' + '.intVal($emp->late_in).' minute'));
						$late = ($punch_in > $late_in) ? 'Y' : '';
						
						$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
						$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
						$interval = $time1->diff($time2);
						$total_hours = $interval->format('%H:%I:%S');
						$total_is = $interval->format('%I:%S');
						
						$punch_in = date('H:i', strtotime($punch_in));
						$punch_out = date('H:i', strtotime($punch_out));
						$total_hours = date('H:i', strtotime($total_hours));
						
						// Total Hours Calculation
						$totalHrs = explode(':',$total_hours);
						// Get Extra Hours
						$exHrsTime = '--:--';$exTime = 0;
						$exHrsData = $this->attendance->getExtraHours(date("Y-m-d",strtotime($currentDate)),$emp->id);
						//print_r($exHrsData);exit;
						if(!empty($exHrsData))
						{
							$exTime = (intVal($exHrsData->ex_hours) * 3600) + (intVal($exHrsData->ex_mins) * 60);
							$exh = (!empty($exHrsData->ex_hours)) ? $exHrsData->ex_hours : '00';
							$exm = (!empty($exHrsData->ex_mins)) ? $exHrsData->ex_mins : '00';
							$exHrsTime = $exh.":".abs($exm);
						}
						// Shift Time Calculation
						$totalShiftTime = explode(':',$emp->total_shift_time);
						$stime = 0;
						if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
							$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
						endif;
						
						$all_puch = sortDates($punchTimes);$lunch_in = '--:--';$lunch_out = '--:--';$totalPunches = count($all_puch);
						$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
						foreach($all_puch as $punch)
						{
							$twh = 0;
							$tm = explode(':',$punch);
							if(intVal($tm[0]) > 0 OR intVal($tm[1]) > 0):
								$twh = (intVal($tm[0]) * 3600) + (floatVal($tm[1]) * 60);
							endif;
							
							if($t==1){$punch_in = $punch;}
							if($t==2){$lunch_in = $punch;}
							if($t==3){$lunch_out = $punch;}
							$wph[$idx][]=$twh;
							if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
							$t++;
						}
						if($totalPunches > 1){$punch_out = $all_puch[$totalPunches - 1];}else{$punch_out = '--:--';}
						
						$allPunches = implode(', ',sortDates($punchTimes));
						
						$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
						
						$wh = intVal($TWHRS) - intVal($ot);
						
						$work_hours = floor($wh / 3600) .':'. floor($wh / 60 % 60);
						$ot = floor($ot / 3600) .':'. floor($ot / 60 % 60);
						$TWHRS += $exTime;$monthWH +=$TWHRS;
						$TWHRS = floor($TWHRS / 3600) .':'. floor($TWHRS / 60 % 60);
						
						
						if($day == 'Wed'){$total_hours = '--:--';$overtime = date('H:i', strtotime($total_hours));}
						
						$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$punch_in.'</td>';
						$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$lunch_in.'</td>';
						$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$lunch_out.'</td>';
						$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$punch_out.'</td>';
						$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$TWHRS.'</td>';
						$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$ot.'</td>';
						$status .= '<th style="border:1px solid #888;text-align:center;color:#00aa00;font-size:12px;width:40px;">P</th>';
						
						$present++;
					}
					else
					{
						$attend_status = false;
						$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$status .= '<th style="border:1px solid #888;text-align:center;color:#cc0000;font-size:12px;width:40px;">A</th>';
						$absent++;
					}
				}
				
				$inData .= '</tr>';$outData .= '</tr>';$lunchInData .= '</tr>';
				$lunchOutData .= '</tr>';$workHrs .= '</tr>';$otData .= '</tr>';$status .= '</tr>';
				
				//$wh = $wh + intVal($wi / 60);$wi = intVal(floatVal($wi) % 60);$wh = $wh.':'.$wi;
				$mwh = floor($monthWH / 3600) .':'. floor($monthWH / 60 % 60);
				
				$empTable = '<table class="table-bordered" style="border:1px solid #888;margin-bottom:10px;">';
				$empTable .='<tr style="background:#eeeeee;">';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">Empcode</th>';
					$empTable .='<th style="border:1px solid #888;text-align:center;font-size:12px;" colspan="2">'.$ecode.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;" colspan="2">Name</th>';
					$empTable .='<th style="border:1px solid #888;text-align:left;font-size:12px;" colspan="'.($last_day - 20).'">'.$emp->emp_name.'</th>';
					$empTable .='<th style="border:1px solid #888;color:#00aa00;font-size:12px;" colspan="2">Present</th>';
					$empTable .='<th style="border:1px solid #888;color:#00aa00;font-size:12px;">'.$present.'</th>';
					$empTable .='<th style="border:1px solid #888;color:#cc0000;font-size:12px;" colspan="2">Absent</th>';
					$empTable .='<th style="border:1px solid #888;color:#cc0000;font-size:12px;">'.$absent.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">LV</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">'.$leave.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">WO</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">'.$wo.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">WH</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;" colspan="2">'.$mwh.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;" colspan="2">Total OT</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">'.$oth.'</th>';
				$empTable .='</tr>';
					
				$empTable .='<tr><td rowspan="2" style="border:1px solid #888;font-size:12px;text-align:center;">#</td>'.$theadDate.'</tr>';
				$empTable .='<tr>'.$theadDay.'</tr>';
				$empTable .= $inData.$lunchInData.$lunchOutData.$outData.$workHrs.$otData.$status;
				$empTable .= '</table>';
				$response .= $empTable;
				if($empCount == 4){$pageData[] = $response;$response='';$empCount=1;}else{$empCount++;}
			}
		}
		$pageData[] = $response;
		// print_r('<hr>');
		// print_r($pageData);
		// print_r('<hr>');exit;
		if($file_type == 'excel')
		{
			$xls_filename = 'monthlyAttendance.xls';
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$xls_filename);
			header('Pragma: no-cache');
			header('Expires: 0');
			
			echo $response;
		}
		else
		{
			$htmlHeader = '<div class="table-wrapper">
								<table class="table txInvHead">
									<tr class="txRow">
										<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
										<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
									</tr>
								</table>
							</div>';
			$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
							<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
							</table>';
			
			$mpdf = new \Mpdf\Mpdf();
			$pdfFileName='monthlyAttendance.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetProtection(array('print'));
			
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
			
			foreach($pageData as $page):
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($page);
			endforeach;
			$mpdf->Output($pdfFileName,'I');
		}
        
    }

	public function monthlyAttendanceSummary(){
        $this->load->view($this->monthSummary,$this->data);
    }

    public function printMonthlySummary($dates,$file_type = 'excel'){
	
		set_time_limit(0);
		if(!empty($dates))
		{
			$duration = explode('~',$dates);
			$empData = $this->attendance->getEmployeeList();
			$companyData = $this->attendance->getCompanyInfo();
			$current_month  = date("m");
			//$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));
			
			$fdate = date("Y-m-d 00:00:01",strtotime($duration[0]));
			$tdate  = date("Y-m-d 23:59:59",strtotime($duration[1]));
			
			$first_day = date("d",strtotime($duration[0]));
			$last_day = date("d",strtotime($duration[1]));
			
			$empTable = '';
			$thead ='';$tbody ='';$i=1;$printData='';$empCount = 1;
			$begin = new DateTime($FromDate);
			$end = new DateTime($ToDate);
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			foreach($daterange as $date)
			{
				$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
				$punchData = New StdClass();
				$punchData = $this->biometric->getPunchData($currentDate,$currentDate);
				$punches = json_decode($punchData[0]->punch_data);
				
				if(!empty($empData))
				{
					foreach($empData as $emp)
					{
						$ecode = sprintf("%04d", $emp->biomatric_id);$punchDates = Array();$punchTimes = Array();
						$attend_status = false;$wo = 0;$wh = 0;$wi = 0;$late = '';$allPunches ='';
						$workHrs = '';$otData = '';$status = '';$exOtHrs = '';$totalWorkHrs = '';$lateStatus = '';$exHrs = '';
						$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
						if(!empty($empPucnhes))
						{
							foreach($empPucnhes as $punch)
							{
								$todayPunch = $punches[$punch];							
								$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
								$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
							}
						}							
						// Get Manual Punches
						$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
						if(!empty($mpData))
						{
							foreach($mpData as $mpRow):
								$time = explode(" ",$mpRow->punch_in);
								$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
								$punchTimes[] = date("H:i:s",strtotime($time[1]));
							endforeach;
						}
						
						if(!empty($punchDates))
						{
							$attend_status = true;
							$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
							$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start));
							$shiftEnd = date('d-m-Y H:i:s', strtotime('+8 hours',strtotime($shiftStart)));
							$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
							if(strtotime($punch_in) > strtotime($shiftEnd))
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($currentDate.' 23:59:59'));
							}
							if( count($punchDates) == 1 ):
								$punch_out = $shiftEnd;
							endif;

							$late_in =  date('d-m-Y H:i:s', strtotime($shiftStart.' + '.intVal($emp->late_in).' minute'));
							$late = ($punch_in > $late_in) ? 'Y' : '';
							
							$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
							$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
							$interval = $time1->diff($time2);
							$total_hours = $interval->format('%H:%I:%S');
							$total_is = $interval->format('%I:%S');
							
							$punch_in = date('H:i', strtotime($punch_in));
							$punch_out = date('H:i', strtotime($punch_out));
							$total_hours = date('H:i', strtotime($total_hours));
							
							// Total Hours Calculation
							$totalHrs = explode(':',$total_hours);
							// Get Extra Hours
							$exHrsTime = '--:--';$exTime = 0;
							$exHrsData = $this->attendance->getExtraHours(date("Y-m-d",strtotime($currentDate)),$emp->id);
							//print_r($exHrsData);exit;
							if(!empty($exHrsData))
							{
								$exTime = (intVal($exHrsData->ex_hours) * 3600) + (intVal($exHrsData->ex_mins) * 60);
								$exh = (!empty($exHrsData->ex_hours)) ? $exHrsData->ex_hours : '00';
								$exm = (!empty($exHrsData->ex_mins)) ? $exHrsData->ex_mins : '00';
								$exHrsTime = $exh.":".abs($exm);
							}
							// Shift Time Calculation
							$totalShiftTime = explode(':',$emp->total_shift_time);
							$stime = 0;
							if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
								$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
							endif;
							
							$all_puch = sortDates($punchTimes);
							$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
							foreach($all_puch as $punch)
							{
								$twh = 0;
								$tm = explode(':',$punch);
								if(intVal($tm[0]) > 0 OR intVal($tm[1]) > 0):
									$twh = (intVal($tm[0]) * 3600) + (floatVal($tm[1]) * 60);
								endif;
								
								$wph[$idx][]=$twh;
								if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
								$t++;
							}
							$allPunches = implode(', ',sortDates($punchTimes));
							
							$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
							
							$wh = intVal($TWHRS) - intVal($ot);
							
							$work_hours = floor($wh / 3600) .':'. floor($wh / 60 % 60);
							$ot = floor($ot / 3600) .':'. floor($ot / 60 % 60);
							$TWHRS += $exTime;
							$TWHRS = floor($TWHRS / 3600) .':'. floor($TWHRS / 60 % 60);
							
							$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">P</td>';
							$workHrs = '<td style="text-align:center;font-size:12px;">'.$work_hours.'</td>';
							$exHrs = '<td style="text-align:center;font-size:12px;">'.$exHrsTime.'</td>';
							$otData = '<td style="text-align:center;font-size:12px;">'.$ot.'</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.$TWHRS.'</td>';
							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';	
						}
						else
						{
							$attend_status = false;
							$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';
							$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';
						}
					
						$empTable .='<tr>';
							$empTable .='<td style="text-align:center;font-size:12px;">'.$ecode.'</td>';
							$empTable .='<td style="text-align:left;font-size:12px;">'.$emp->emp_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$emp->name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$emp->shift_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$currentDate.'</td>';
							$empTable .= $status.$workHrs.$exHrs.$otData.$totalWorkHrs.$lateStatus;
							$empTable .='<td style="font-size:12px;text-align:left;">'.$allPunches.'</td>';
						$empTable .='</tr>';
					}
				}
			}
			
			
			
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
				$response .= '<thead>
						<tr style="background:#eee;">
							<th>Emp Code</th>
							<th>Employee</th>
							<th>Department</th>
							<th>Shift</th>
							<th>Punch Date</th>
							<th>Status</th>
							<th>WH</th>
							<th>Ex. Hours</th>
							<th>OT</th>
							<th>TWH</th>
							<th>Late</th>
							<th>All Pucnhes</th>
						</tr></thead><tbody>'.$empTable.'</tbody></table>';
						
			if($file_type == 'excel')
			{
				$xls_filename = 'monthlyAttendance.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			}
			else
			{
				$htmlHeader = '<div class="table-wrapper">
									<table class="table txInvHead">
										<tr class="txRow">
											<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
											<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName='monthlyAttendance.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			}
			
		}
	}

}
?>