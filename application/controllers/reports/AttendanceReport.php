<?php
class AttendanceReport extends MY_Controller
{
	private $indexPage = "report/hr_report/index";
	private $emp_report = "report/hr_report/emp_report";
	private $monthlyAttendance = "report/hr_report/month_attendance";
	private $monthSummary = "report/hr_report/month_summary";
	private $monthlySummary = "report/hr_report/monthly_summary";
	private $empRole = ["1" => "Admin", "2" => "Production Manager", "3" => "Accountant", "4" => "Sales Manager", "5" => "Purchase Manager", "6" => "Employee", "7" => "HR", "8" => "Vendor"];

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "HR Report";
		$this->data['headData']->controller = "reports/attendanceReport";
		$this->data['floatingMenu'] = $this->load->view('report/hr_report/floating_menu',[],true);
	}

	public function index(){
		$this->data['pageHeader'] = 'HR REPORT';
		$this->load->view($this->indexPage, $this->data);
	}
	
	// Daterange Attendance Summary
	public function monthlyAttendanceSummary(){
		$this->data['headData']->pageTitle = "Attendance Summary";
		$this->data['empList'] = $this->employee->getEmployeeList('emp_code');
		$this->data['shiftList'] = $this->shiftModel->getShiftList();
		$this->load->view($this->monthSummary, $this->data);
	}

	public function printMonthlySummary($dates,$biomatric_id="ALL",$shift_id="ALL",$file_type = 'excel'){		
		set_time_limit(0);
		if(!empty($dates))
		{
			$duration = explode('~',$dates);
			$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
			if($shift_id=="ALL"){$shift_id="";}
			$empData = $this->employee->getEmpListForReport(['biomatric_id'=>$biomatric_id]);
			//print_r($empData);exit;
			$companyData = $this->attendance->getCompanyInfo();
			$current_month  = date("m");
			$month  = date("m",strtotime($duration[0]));
			$year  = date("Y",strtotime($duration[0]));
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));
			
			$fdate = date("Y-m-d 00:00:01",strtotime($duration[0]));
			$tdate  = date("Y-m-d 23:59:59",strtotime($duration[1]));
			
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
				$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
				$currentDay = date('D', strtotime($currentDate));
				if(!empty($empData))
				{
					foreach($empData as $row)
					{
						$shift_name = '';$allPunches = '';$status="";$workHrs="";$ltTd="";$exHrs="";$otData="";$totalWorkHrs="";
						//if(!empty($row->emp_joining_date) AND (strtotime($row->emp_joining_date) <= strtotime($currentDate)))
					    //{
							$empAttendanceLog = $this->biometric->getEmpPunchesByDate($row->id, $currentDate,$shift_id);
							//print_r($empAttendanceLog);exit;
							if(!empty($empAttendanceLog))
							{
							    $actualPunches = Array();
    							$empPunches = array_column($empAttendanceLog, 'punch_date');
    							$shift_name = array_column($empAttendanceLog, 'shift_name')[0];
    							if(!empty($empPunches[0]))
    							{
    								
    								$empPunches = explode(',',$empPunches[0]);							
    								$punch_type = array_column($empAttendanceLog, 'punch_type');
    								
    								$ts_time = array_column($empAttendanceLog, 'ts_time')[0]; // Total Shift Time
    								$shift_start_time = array_column($empAttendanceLog, 'shift_start_time')[0];
    								$lunch_time = array_column($empAttendanceLog, 'lunch_time')[0];
    								$lunch_start = array_column($empAttendanceLog, 'lunch_start')[0];
    								$lunch_end = array_column($empAttendanceLog, 'lunch_end')[0];
    								$shift_type = array_column($empAttendanceLog, 'shift_type')[0];
    								$xmins = array_column($empAttendanceLog, 'xmins')[0];
    								$startLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_start));
    								$endLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_end));
    								
    								$sortType = ($shift_type == 1) ? 'ASC' : 'ASC';
    								$empPunches = sortDates($empPunches,$sortType);
    								$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
    								$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
    								
    								$actualPunches = $empPunches;
    								if(!empty($empPunches[0]) AND $empPunches[0]< $shift_start_time){$empPunches[0]=$shift_start_time;}
    								
    								$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';
    								// Count Total Time [1-2,3-4,5-6.....]
    								foreach($empPunches as $punch)
    								{
    									$wph[$idx][]=strtotime($punch);
    									if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
    									$t++;
    								}
    								$twh = $stay_time;
    								
    								// Reduce Lunch Time
    								if((strtotime($punch_in) < strtotime($startLunch)) AND (strtotime($punch_out) > strtotime($endLunch)))
    								{
    									$countedLT = 0;
    									if(count($empPunches) > 2){$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);$twh += $countedLT;}
    									//if($countedLT>=2){$countedLT = floatVal($wph[1][0]) - floatVal($wph[0][1]);}
    									if($countedLT > $lunch_time){$lunch_time = $countedLT;}
    									$twh = $twh - $lunch_time;
    								}
    								
    								// Get Extra Hours
        							$exHrsTime = '<td style="text-align:center;font-size:12px;">--:--</td>';$exTime = 0;    							
        							if(!empty($xmins))
        							{
        								$exTime = intVal($xmins) * 60;
    									$xhours = intVal($xmins/60);
    									$xmins = intVal($xmins) - ($xhours * 60);
        								$exh = (!empty($xhours)) ? $xhours : '00';
        								$exm = (!empty($xmins)) ? $xmins : '00';
        								
        								if($xhours < 0 OR $xmins < 0):
        								    $exHrsTime = '<td style="text-align:center;color:#aa0000;font-size:12px;font-weight:bold;">'.abs($exh).' H : '.abs($exm).' M</td>';
        								else:
        								    $exHrsTime = '<td style="text-align:center;font-size:12px;">'.abs($exh).' H : '.abs($exm).' M</td>';
        								endif;
    									
    									$twh += $exTime;
        							}
    								
    								// Count Overtime and Working Time as per shift
    								if($twh > $ts_time){$wh = $ts_time;$ot = $twh - $ts_time;}else{$wh = $twh;}
    								
    								$ap = Array();
    								foreach($actualPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
    								$allPunches = implode(', ',$ap);
    								
    								// Check For Missed Punch
    								if(count($empPunches) % 2 != 0)
    								{
    									$status = '<td style="text-align:center;color:#233288;font-size:12px;">M</td>';
    								}
    								else
    								{
    									if($twh <= 0){$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';}
    									else{$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">'.$present_status.'</td>';}
    								}
    								$workHrs = '<td style="text-align:center;font-size:12px;">'.formatSeconds($wh).'</td>';
    								$ltTd = '<td style="text-align:center;font-size:12px;">'.formatSeconds($lunch_time).'</td>';
    								$exHrs = $exHrsTime;//'<td style="text-align:center;font-size:12px;">'.$exHrsTime.'</td>';
    								$otData = '<td style="text-align:center;font-size:12px;">'.formatSeconds($ot).'</td>';
    								$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.formatSeconds($twh).'</td>';
    							}
    							else
    							{
    								$attend_status = false;if($currentDay == 'Wed'){$present_status = 'W';}else{$present_status='A';}
    								$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">'.$present_status.'</td>';
    								$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
    								$ltTd = '<td style="text-align:center;font-size:12px;">--:--</td>';
    								$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
    								$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
    								$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							}
							}
							else
    						{
    							$shift_name = 'NA';$allPunches='';
    							$status = '<td style="text-align:center;;font-size:12px;">NA</td>';
    							$workHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
    							$ltTd = '<td style="text-align:center;font-size:12px;">NA</td>';
    							$exHrs= '<td style="text-align:center;font-size:12px;">NA</td>';
    							$otData = '<td style="text-align:center;font-size:12px;">NA</td>';
    							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
    						}
						/*}
						else
						{
							$shift_name = 'NA';$allPunches='NA';
							$status = '<td style="text-align:center;;font-size:12px;">NA</td>';
							$workHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
							$ltTd = '<td style="text-align:center;font-size:12px;">NA</td>';
							$exHrs= '<td style="text-align:center;font-size:12px;">NA</td>';
							$otData = '<td style="text-align:center;font-size:12px;">NA</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
						}*/
						//if(!empty($allPunches))
						//{
							$empTable .='<tr>';
								$empTable .='<td style="text-align:center;font-size:12px;">'.$row->emp_code.'</td>';
								$empTable .='<td style="text-align:left;font-size:12px;">'.$row->emp_name.'</td>';
								$empTable .='<td style="font-size:12px;">'.$row->dept_name.'</td>';
								$empTable .='<td style="font-size:12px;">'.$shift_name.'</td>';
								$empTable .='<td style="font-size:12px;">'.date("d-m-Y",strtotime($currentDate)).'</td>';
								$empTable .= $status.$workHrs.$ltTd.$exHrs.$otData.$totalWorkHrs;
								$empTable .='<td style="font-size:12px;text-align:left;">'.$allPunches.'</td>';
							$empTable .='</tr>';
						//}
					}
				}
			}
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
				$response .= '<thead>
						<tr style="background:#eee;">
							<th style="width:100px;">Emp Code</th>
							<th>Employee</th>
							<th>Department</th>
							<th>Shift</th>
							<th>Punch Date</th>
							<th>Status</th>
							<th>WH</th>
							<th>Lunch</th>
							<th>Ex. Hours</th>
							<th>OT</th>
							<th>TWH</th>
							<th style="width:250px;">All Pucnhes</th>
						</tr></thead><tbody>'.$empTable.'</tbody></table>';
			//echo $response;exit;
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
											<td class="text-right pad-right-10"><b>Date Period : '.date("d-m-Y",strtotime($duration[0])).' TO '.date("d-m-Y",strtotime($duration[1])).'</td>
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
				//$mpdf->Output($pdfFileName,'I');
				
				//$mpdf->Output($pdfFileName,'I');
				if($file_type == 'mail'):
					$fileName= 'Monthy_Summury_'.date("d-m-Y",strtotime($duration[0])).' TO '.date("d-m-Y",strtotime($duration[1])).'.pdf';
					$filePath = realpath(APPPATH . '../assets/uploads/attendance/');
					$mpdf->Output($filePath.'/'.$fileName, 'F');
					return ['pdf_file'=>$filePath.'/'.$fileName,'fdate'=>date("d-m-Y",strtotime($duration[0])),'tdate'=>date("d-m-Y",strtotime($duration[1]))];
				else:
					$mpdf->Output($fileName,'I');
				endif;
			}
		}
	}
	
	// Monthly Attendance Hourly
	public function monthlyAttendance(){
		$this->data['headData']->pageTitle = "Monthly Attendance";
		$this->data['deptRows'] = $this->department->getDepartmentList();
		$this->load->view($this->monthlyAttendance, $this->data);
	}
	
	public function getHourlyReport($month="",$dept_id="",$file_type = 'pdf',$record_limit="",$biomatric_id="ALL"){
		
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$month = $this->input->post('month');
			$dept_id = $this->input->post('dept_id');
			$file_type = $this->input->post('file_type');
			$record_limit = $this->input->post('record_limit');
		}
		$postData['month'] = date('Y-m-d',strtotime($month));
		$postData['dept_id'] = $dept_id;
		$postData['file_type'] = $file_type;
		$postData['record_limit'] = $record_limit; 
		$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		
		if(!empty($month))
		{
			$empData = $this->biometric->getEmpShiftLog($postData);			
			$lastDay = intVal(date('t',strtotime($postData['month'])));
			
			$tbody='';$i=0;
			$thead='<tr style="background:#dddddd;"><th style="width:50px;">Code</th><th style="width:220px;">Emp Name</th>';
			for($d=1;$d<=$lastDay;$d++){$thead.='<th>'.$d.'</th>';}
			$thead.='<th>Total</th></tr>';
			if(!empty($empData))
			{
				foreach($empData as $row)
				{
					$tr_bg = ($i % 2 == 0) ? '' : '#efefef';$i++;
					$tmwh = 0;$dept_name = $row->dept_name;
					$tbody.='<tr style="background:'.$tr_bg.'">';
					$tbody.='<td class="text-center">'.$row->emp_code.'</td>';
					$tbody.='<td>'.$row->emp_name.'</td>';
					for($d=1;$d<=$lastDay;$d++)
					{
						$day = str_pad($d, 2, '0', STR_PAD_LEFT);
						$currentDate = date('Y-m-'.$day,strtotime($postData['month']));
						$empAttendanceLog = $this->biometric->getEmpPunchesByDateAlog($row->emp_id,$currentDate);
						
						$empPunches = array_column($empAttendanceLog, 'punch_date');
						if(!empty($empPunches[0]))
						{
							$empPunches = explode(',',$empPunches[0]);
							
							$ts_time = array_column($empAttendanceLog, 'ts_time')[0]; // Total Shift Time
							$lunch_time = array_column($empAttendanceLog, 'lunch_time')[0];
							$lunch_start = array_column($empAttendanceLog, 'lunch_start')[0];
							$lunch_end = array_column($empAttendanceLog, 'lunch_end')[0];
							$shift_type = array_column($empAttendanceLog, 'shift_type')[0];
							$shift_name = array_column($empAttendanceLog, 'shift_name')[0];
							$xmins = array_column($empAttendanceLog, 'xmins')[0];
							$startLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_start));
							$endLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_end));
							
							$sortType = ($shift_type == 1) ? 'ASC' : 'ASC';
							$empPunches = sortDates($empPunches,$sortType);
							$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
							$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
							
							$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';
							// Count Total Time [1-2,3-4,5-6.....]
							foreach($empPunches as $punch)
							{
								$wph[$idx][]=strtotime($punch);
								if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
								$t++;
							}
							$twh = $stay_time;
							
							// Reduce Lunch Time
							if((strtotime($punch_in) < strtotime($startLunch)) AND (strtotime($punch_out) > strtotime($endLunch)))
							{
								$countedLT = 0;
								if(count($empPunches) > 2){$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);$twh += $countedLT;}
								//if($countedLT>=2){$countedLT = floatVal($wph[1][0]) - floatVal($wph[0][1]);}
								if($countedLT > $lunch_time){$lunch_time = $countedLT;}
								$twh = $twh - $lunch_time;
							}
							
							// Get Extra Hours
							$exHrsTime = '<td style="text-align:center;font-size:12px;">--:--</td>';$exTime = 0;    							
							if(!empty($xmins))
							{
								$exTime = intVal($xmins) * 60;								
								$twh += $exTime;
							}
							
							// Count Overtime and Working Time as per shift
							if($twh > $ts_time){$wh = $ts_time;$ot = $twh - $ts_time;}else{$wh = $twh;}
							$tmwh += $twh;
							$tbody.='<td class="text-center">'.round(($twh/3600),2).'</td>'; // Hours 11.5
						}
						else{$tbody.='<td class="text-center"></td>';}
					}
					$tbody.='<td class="text-center" style="width:45px;">'.round(($tmwh/3600),2).'</td>'; // Hours 11.5
					$tbody.='</tr>';
				}
			}
			$tableHeader = '';if(empty($postData['dept_id'])){$dept_name = 'All Department';};
			$tableHeader = '<tr style="background:#dddddd;"><th colspan="2" class="text-left">'.$dept_name.'</th>';
			$tableHeader .= '<th colspan="'.($lastDay-5).'">'.$companyData->company_name.'</th>';
			$tableHeader .= '<th colspan="6" class="text-right">'.date("F-Y",strtotime($postData['month'])).'</th></tr>';
			
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
			$response .= '<thead>'.$tableHeader.$thead.'</thead>';
			$response .= '<tbody>'.$tbody.'</tbody></table>';
 			
			if($file_type == 'excel')
			{
				$xls_filename = 'monthlyAttendance_'.$dept_name.'.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			}
			elseif($file_type == 'pdf')
			{
				$htmlHeader = '<div class="table-wrapper">
									<table class="table txInvHead">
										<tr class="txRow">
											<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
											<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($postData['month'])).'</td>
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
			else
			{
				$this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody]);
			}
		}
	}
	
	// Missed Punch
	public function mismatchPunch(){
		$this->data['headData']->pageTitle = "Employee Punches";
        $this->load->view("report/hr_report/mismatch_punch",$this->data);
    }
	
	public function getAllPunches($report_date="",$punch_status=""){
		
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$report_date = $this->input->post('report_date');
			$punch_status = $this->input->post('punch_status');
		}
		$postData['report_date'] = date('Y-m-d',strtotime($report_date));
		$postData['punch_status'] = $punch_status;
		//$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		//print_r($postData['punch_status']);exit;
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getAllPunches($postData);
			$html = "";
			foreach($mpData as $row):
				$allPunches =  '';$mcls = "";
				if($row->punchCount > 0)
				{
					$empPunches = explode(',',$row->punch_date);
					$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
					$empPunches = sortDates($empPunches,$sortType);
					//$ap = Array();$mcls = (($row->punchCount % 2 != 0) OR ($row->punchCount > 4)) ? 'text-danger' : '';
					$ap = Array();$mcls = ($row->punchCount % 2 != 0) ? 'text-danger' : '';
					foreach($empPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
					$allPunches = implode(', ',$ap);
				}
				$html .= '
					<tr>
						<td>'.$row->emp_code.'</td>
						<td>'.$row->emp_name.'</td>
						<td>'.$row->dept_name.'</td>
						<td>'.$row->shift_name.'</td>
						<td>'.$row->emp_dsg.'</td>
						<td class="text-left '.$mcls.'">'.$allPunches.'</td>
						<td class="text-center"><a href="#" class="float-right manualAttendance btn btn-sm btn-success" data-button="close" data-empid="'.$row->emp_id.'" data-adate="'.$report_date.'" data-button="both" data-modal_id="modal-lg" data-function="addManualAttendance" data-form_title="Add Manual Attendance"> View ('.$row->punchCount.')</a></td>
					</tr>
				';
			endforeach;
			$this->printJson(['status'=>1,'tbody'=>$html]);
		}
	}
	
	// Get Daily Attendance (Used In Attendance Dashboard)
	public function getDailyAttendance($report_date=""){
		
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$report_date = $this->input->post('report_date');
		}
		$postData['report_date'] = date('Y-m-d',strtotime($report_date));
		//$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getPunchByDate($postData['report_date']);
			
			$empTable = "";$totalEmp=0;$presentEmp=0;$absentEmp=0;$lateEmp=0;
			if(!empty($mpData))
			{
				$totalEmp=count($mpData);
				foreach($mpData as $row):
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
					
					$empTable .= '
						<tr>
							<td>'.$row->emp_code.'</td>
							<td>'.$row->emp_name.'</td>
							<td>'.$row->dept_name.'</td>
							<td>'.$row->shift_name.'</td>
							<td>'.$row->emp_dsg.'</td>
							<td>'.$status.'</td>
							<td>'.$allPunches.'</td>
						</tr>
					';
				endforeach;
				$this->printJson(['status'=>1,"totalEmp"=>$totalEmp,"present"=>$presentEmp,"late"=>$lateEmp,"absent"=>$absentEmp,'tbody'=>$empTable]);
			}
			else
			{
				$this->printJson(['status'=>1,"totalEmp"=>0,"present"=>0,"late"=>0,"absent"=>0,'tbody'=>""]);
			}
		}
	}

	public function getAbsentReport($fDate="",$file_type='pdf'){
		
		set_time_limit(0);
		
		if(empty($fDate)){$postData['report_date'] = date('Y-m-d');}
		else{$postData['report_date'] = date('Y-m-d',strtotime($fDate));}
		$companyData = $this->attendance->getCompanyInfo();
		$reportData = "";
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getAbsentReport($postData['report_date']);
			$i=1;
			foreach($mpData as $row):
				$reportData .= '<tr>
					<td class="text-center">'.$i++.'</td>
					<td class="text-center">'.$row->emp_code.'</td>
					<td>'.$row->emp_name.'</td>
					<td>'.$row->shift_name.'</td>
					<td>'.$row->dept_name.'</td>
					<td>'.$row->emp_dsg.'</td>
				</tr>';
			endforeach;			
		}
		
		$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
		$response .= '<thead>
				<tr style="background:#eee;"><th colspan="6">Absent Report [ '.date('d-m-Y',strtotime($postData['report_date'])).' ]</th></tr>
				<tr style="background:#eee;">
					<th style="width:50px;">#</th>
					<th style="width:100px;">Emp Code</th>
					<th>Employee</th>
					<th>Shift</th>
					<th>Department</th>
					<th>Designation</th>
				</tr></thead><tbody>'.$reportData.'</tbody></table>';
		//echo $response;exit;
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
										<td class="text-right pad-right-10"><b>Report Date : '.date('d-m-Y',strtotime($postData['report_date'])).'</td>
									</tr>
								</table>
							</div>';
			$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
							<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
							</table>';
			
			$mpdf = new \Mpdf\Mpdf();
			$pdfFileName='absentReport_'.date('d-m-Y',strtotime($postData['report_date'])).'.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetProtection(array('print'));
			$mpdf->SetTitle('Absent Report '.date('d-m-Y',strtotime($postData['report_date'])));
			
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
			
			$mpdf->AddPage('P','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-P');
			$mpdf->WriteHTML($response);
			$mpdf->Output($pdfFileName,'I');
		}
	}
	
	// Print Monthly Attendance EMP WISE | 04.12.2022
	public function printMonthlyAttendance($dept_id,$month, $file_type = 'excel',$record_limit="",$chunk = '-1')
	{
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$month = $this->input->post('month');
			$dept_id = $this->input->post('dept_id');
			$file_type = $this->input->post('file_type');
		}
		$postData['month'] = date('Y-m-d',strtotime($month));
		$postData['dept_id'] = $dept_id;
		$postData['file_type'] = $file_type;
		$postData['record_limit'] = $record_limit; 
		$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		//$postData['biomatric_id']='20543';
		if(!empty($month))
		{
			$empData = $this->biometric->getEmpShiftLog($postData);
			
			/*$parts = intVal(count($empRows)/10);
	        $empCh = array_chunk($empRows,$parts);
	         
			if( $chunk >=  0){$empData = (!empty($empCh[$chunk]) ? $empCh[$chunk] : []);}
			else
			{
			    if(count($empRows) > 20){$empData = (!empty($empCh[0]) ? $empCh[0] : []);}else{$empData = $empRows;}
			}*/
			$department = "All";
			$lastDay = intVal(date('t',strtotime($postData['month'])));
            
			$punchData = NULL;$thead = '';$tbody = '';$i = 1;$printData = '';$empCount = 1;
			
			$response = '';$empTable = '';$pageData = array();
			if (!empty($empData)) {
				foreach ($empData as $emp) {
					$present = 0;$leave = 0;$absent = 0;$theadDate = '';$theadDay = '';
					$wo = 0;$wh = 0;$wi = 0;$oth = 0;$oti = 0;$ot=0;$totalWH= 0;$totalOT = 0;$totalTWH = 0;
					$inData = '';$outData = '';$lunchInData = '';$lunchOutData = '';
					$workHrs = '';$otData = '';$status = '';

					$inData .= '<tr><th style="border:1px solid #888;font-size:12px;">IN</th>';
					$lunchInData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-START</th>';
					$lunchOutData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-END</th>';
					$outData .= '<tr><th style="border:1px solid #888;font-size:12px;">OUT</th>';
					$workHrs .= '<tr><th style="border:1px solid #888;font-size:12px;">WH</th>';
					$otData .= '<tr><th style="border:1px solid #888;font-size:12px;">OT</th>';
					$status .= '<tr><th style="border:1px solid #888;font-size:12px;">STATUS</th>';
					for($d=1;$d<=$lastDay;$d++){
						$attend_status = false;
						$dt = str_pad($d, 2, '0', STR_PAD_LEFT);
						$currentDate = date('Y-m-'.$dt,strtotime($postData['month']));
						$punchDates = array();
						$day = date("D", strtotime($currentDate));
						if ($day == 'Wed') {$wo++;}
						$theadDate .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">' . $d . '</th>';
						$theadDay .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">' . $day . '</th>';
						
						$empAttendanceLog = Array();$empPunches = Array();
						
						$empAttendanceLog = $this->biometric->getEmpPunchesByDateAlog($emp->emp_id,$currentDate);
					    $empPunches = array_column($empAttendanceLog, 'punch_date');
						
						if(!empty($empPunches[0]))
						{
							$empPunches = explode(',',$empPunches[0]);
							
							$ts_time = array_column($empAttendanceLog, 'ts_time')[0]; // Total Shift Time
							$lunch_time = array_column($empAttendanceLog, 'lunch_time')[0];
							$lunch_start = array_column($empAttendanceLog, 'lunch_start')[0];
							$lunch_end = array_column($empAttendanceLog, 'lunch_end')[0];
							$shift_type = array_column($empAttendanceLog, 'shift_type')[0];
							$shift_name = array_column($empAttendanceLog, 'shift_name')[0];
							$shift_start_time = array_column($empAttendanceLog, 'shift_start_time')[0];
							$xmins = array_column($empAttendanceLog, 'xmins')[0];
							$startLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_start));
							$endLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_end));
							
							$sortType = ($shift_type == 1) ? 'ASC' : 'ASC';
							$empPunches = sortDates($empPunches,$sortType);
							
							// Trim Early In by 15 Minutes
							$ltSecond = ((strtotime(min($empPunches)) - strtotime($shift_start_time))/60);
							if($ltSecond < 0){if(abs($ltSecond) < 15){$empPunches[0] = date('Y-m-d H:i:s', (strtotime($empPunches[0]) + (abs($ltSecond)*60)));}}
							
							$punch_in = date('H:i', strtotime(min($empPunches)));
							$punch_out = date('H:i', strtotime(max($empPunches)));
							
							$lunch_in = '--:--';
							$lunch_out = '--:--';
							
							$totalPunches = count($empPunches);
							if (intVal($totalPunches) > 2) :
								$lunch_in = date('H:i', strtotime($empPunches[1]));
								if (intVal($totalPunches) > 3) :
									$lunch_out = date('H:i', strtotime($empPunches[2]));
								endif;
							endif;
							
							$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';
							// Count Total Time [1-2,3-4,5-6.....]
							foreach($empPunches as $punch)
							{
								$wph[$idx][]=strtotime($punch);
								if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
								$t++;
							}
							
							$twh = $stay_time;
							
							//if($shift_type == 2){$lunch_time=0;}
							
							// Reduce Lunch Time
							if((strtotime(min($empPunches)) < strtotime($startLunch)) AND (strtotime(max($empPunches)) > strtotime($endLunch)) AND intval(date('H', strtotime($empPunches[0]))) < 20)
							{
								$countedLT = 0;
								if(count($empPunches) > 2){$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);$twh += $countedLT;}
								if($countedLT > $lunch_time){$lunch_time = $countedLT;}
								$twh = $twh - $lunch_time;
							} 
							// Reduce Lunch for Night Shift
							if(intval(date('H', strtotime($empPunches[0]))) >= 20){
							    $lunch_time=3600;
							    //$twh = (($twh >= (8.5*3600)) ? ($twh -= $lunch_time) : 0);
							    if($twh >= (8.5*3600)){$twh -= $lunch_time;}
							}
							
							// Get Extra Hours
							$exTime = 0;
							if(!empty($xmins)){$exTime = intVal($xmins) * 60;$twh += $exTime;}
							
							// Count Overtime and Working Time as per shift
							if($twh > $ts_time){$wh = $ts_time;$ot = $twh - $ts_time;}else{$wh = $twh;}
							
							$totalWH += $wh;$totalOT += $ot;$totalTWH += $twh;
							
							$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $punch_in . '</td>';
							$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $lunch_in . '</td>';
							$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $lunch_out . '</td>';
							$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $punch_out . '</td>';
							$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . s2hi($wh) . '</td>';
							$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . s2hi($ot) . '</td>';
							$dayStatus = 'P';
							if($ltSecond > 10){$dayStatus = 'L';}
							if ($day == 'Wed') {$dayStatus .= 'W';}
							
							$status .= '<th style="border:1px solid #888;text-align:center;color:#00aa00; font-size:12px;width:40px;">'.$dayStatus.'</th>';
							

							$present++;
						}
						else{
							$attend_status = false;
							$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$dayStatus = 'WO';
							if ($day != 'Wed') {$dayStatus = 'A';$absent++;}
							$status .= '<th style="border:1px solid #888;text-align:center;color:#cc0000;font-size:12px;width:40px;">'.$dayStatus.'</th>';
							
						}
					}

					$inData .= '</tr>';
					$outData .= '</tr>';
					$lunchInData .= '</tr>';
					$lunchOutData .= '</tr>';
					$workHrs .= '</tr>';
					$otData .= '</tr>';
					$status .= '</tr>';
					
                    $department = $emp->dept_name;
					$empTable = '<table class="table-bordered" style="border:1px solid #888;margin-bottom:10px;">';
					$empTable .= '<tr style="background:#eeeeee;">';
					//$empTable .= '<th style="border:1px solid #888;font-size:12px;">Empcode</th>';
					$empTable .= '<th style="border:1px solid #888;text-align:left;font-size:12px;" colspan="'.($lastDay - 15).'">' . $emp->emp_code . ' - ' . $emp->emp_name . '</th>';
					//$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="2">Name</th>';
					//$empTable .= '<th style="border:1px solid #888;text-align:left;font-size:12px;" colspan="' . ($lastDay - 18) . '">' . $emp->emp_name . '</th>';
					$empTable .= '<th style="border:1px solid #888;color:#00aa00;font-size:12px;" colspan="3">Present : ' . $present . '</th>';
					$empTable .= '<th style="border:1px solid #888;color:#cc0000;font-size:12px;" colspan="3">Absent : ' . $absent . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="2">LV : ' . $leave . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="2">WO : ' . $wo . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="3">WH : ' . s2hi($totalTWH) . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="3">Total OT : ' . s2hi($totalOT) . '</th>';
					$empTable .= '</tr>';

					$empTable .= '<tr><td rowspan="2" style="border:1px solid #888;font-size:12px;text-align:center;">#</td>' . $theadDate . '</tr>';
					$empTable .= '<tr>' . $theadDay . '</tr>';
					$empTable .= $inData . $lunchInData . $lunchOutData . $outData . $workHrs . $otData . $status;
					$empTable .= '</table>';
					$response .= $empTable;
					if ($empCount == 4) {
						$pageData[] = $response;
						$response = '';
						$empCount = 1;
					} else {$empCount++;}
				}
			}
			$pageData[] = $response;
			
			/*foreach ($pageData as $page) :
				echo $page;
			endforeach;
			exit;*/
			
			if ($file_type == 'excel') {
				$xls_filename = 'monthlyAttendance.xls';

				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename=' . $xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				foreach ($pageData as $page) :
					echo $page;
				endforeach;
			} else {
				$htmlHeader = '<div class="table-wrapper">
									<table class="table txInvHead">
										<tr class="txRow">
											<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">' . $companyData->company_name . '</td>
											<td class="text-right pad-right-10"><b>Report Month : ' . date("F-Y", strtotime($postData['month'])) . '</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y} | '.$department.'</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';

				// $mpdf = $this->m_pdf->load();
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName = 'monthlyAttendance.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet, 1);
				$mpdf->SetDisplayMode('fullpage');
				//$mpdf->SetProtection(array('print'));

				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);

				foreach ($pageData as $page) :
					$mpdf->AddPage('L', '', '', '', '', 5, 5, 17, 10, 5, 0, '', '', '', '', '', '', '', '', '', 'A4-L');
					$mpdf->WriteHTML($page);
				endforeach;
				$mpdf->Output($pdfFileName, 'I');
			}
		}
	}

	/* Created By Jp@11.01.2023*/
    public function sendAttendaceMail(){
		$postData = $this->input->post(); 
        $printData = $this->printMonthlySummary($postData['dates'],$postData['biomatric_id'],$postData['shift_id'],'mail');
    	$empData = $this->employee->getEmp($this->loginId);
		if(!empty($printData))
		{
    		$attachment = $printData['pdf_file'];
            $senderEmailId = 'hr@jayjalaramind.com';
            
            $signData['sender_name'] = $empData->emp_name;
            $signData['sender_contact'] = $empData->contact_no;
            $signData['sender_designation'] = $empData->designation;
            $signData['sign_email'] = $senderEmailId;
            
    		$emailSignature = $this->mails->getSignature($signData);
    
    		$mailData = array();
    		$mailData['sender_email'] = $senderEmailId;
    		//$mailData['receiver_email'] = 'jagdishpatelsoft@gmail.com';
    		$mailData['receiver_email'] = 'info@jayjalaramind.com';
    		$mailData['cc_email'] = 'account@jayjalaramind.com,quality@jayjalaramind.com,production@jayjalaramind.com';
    		$mailData['bcc_email'] = $senderEmailId;
    		$mailData['mail_type'] = 7;
    		$mailData['ref_id'] = 0;
    		$mailData['ref_no'] = 0;
    		$mailData['created_by'] = $this->loginId;
    		$mailData['subject'] = 'Attendance Summary - '.$printData['fdate'].' To '.$printData['tdate'];
    		
    		$mail_body = '<div style="font-size:12pt;font-family: Bookman Old Style;">';
    		    $mail_body .= '<b>Dear Team,</b><br><br>';
    		    $mail_body .= 'Wishing you a good day!<br>';
    		    $mail_body .= 'Here, we are enclosing Attendance Summury for Period : <b>'.$printData['fdate'].' To '.$printData['tdate'].'</b><br><br>Please find the attachment.<br><br><br>';
            $mail_body .= '</div>';
    		$mail_body .= $emailSignature;
    		$mailData['mail_body'] = $mail_body;
    		
    		$result = $this->mails->sendMail($mailData, [$attachment]);
    		unlink($attachment);
    		$this->printJson($result);
        }
		else
		{
		    $this->printJson(['status'=>0,'message'=>'Attendance Summary Reprot Not Found']);
		}
	}
}
