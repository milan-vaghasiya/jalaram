<?php
class Payroll extends MY_Controller{
    private $indexPage = "hr/payroll/index";
    private $payrollForm = "hr/payroll/form";
    private $payrollDataPage = "hr/payroll/payroll_data";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Payroll";
		$this->data['headData']->controller = "hr/payroll";
		$this->data['headData']->pageUrl = "hr/payroll";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('payroll');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->payroll->getDTRows($this->input->post());
		$sendData = array();$i=1;
        foreach($result['data'] as $row):      
			$row->sr_no = $i++;
			$row->salary_sum = $this->payroll->getSalarySumByMonth($row->month)->salary_sum;
            $sendData[] = getPayrollData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function loadSalaryForm(){
        $start = new DateTime($this->startYearDate);
        $start->modify('last day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('last day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        //$this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }

    public function getEmpSalaryData(){
        $data = $this->input->post();

        $empData = $this->payroll->getEmployeeListForSalary($data);

        $FromDate = date("Y-m-01",strtotime($data['month']));
        $ToDate  = date("Y-m-d",strtotime($data['month']));
        
        $begin = new DateTime($FromDate);
        $end = new DateTime($ToDate);
        $end = $end->modify( '+1 day' ); 

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        $html = '';$sr_no=1;
        if(!empty($empData)):
            foreach($empData as $row):

                $absent = 0;$present = 0;$total_wh = 0;
                foreach($daterange as $date):
                    $currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
                    $nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                    $currentDay = date('D', strtotime($currentDate));

                    $empAttendanceLog = $this->biometric->getEmpPunchesByDate($row->id, $currentDate,$shift_id="");
                    if(!empty($empAttendanceLog)):
                        $empPunches = array_column($empAttendanceLog, 'punch_date');

                        if(!empty($empPunches[0])):
                            $empPunches = explode(',',$empPunches[0]);							
    						$punch_type = array_column($empAttendanceLog, 'punch_type');

                            $ts_time = array_column($empAttendanceLog, 'ts_time')[0]; // Total Shift Time
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

                            // Count Total Time [1-2,3-4,5-6.....]
                            $wph = Array();$t=1;$idx=0;$twh=0;$stay_time=0;
                            foreach($empPunches as $punch):
                                $wph[$idx][]=strtotime($punch);
    							if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
    							$t++;
                            endforeach;
                            $twh = $stay_time;

                            // Reduce Lunch Time
                            if((strtotime($punch_in) < strtotime($startLunch)) AND (strtotime($punch_out) > strtotime($endLunch))):
                                $countedLT = 0;
                                if(count($empPunches) > 2):$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);$twh += $countedLT;endif;
                                if($countedLT > $lunch_time):$lunch_time = $countedLT;endif;
                                $twh = $twh - $lunch_time;
                            endif;

                            // Get Extra Hours
                            $exTime = 0;
                            if(!empty($xmins)):
                                $exTime = intVal($xmins) * 60;
                                $twh += $exTime;
                            endif;

                            // Count Overtime and Working Time as per shift
    						if($twh > $ts_time):$wh = $ts_time;$ot = $twh - $ts_time;else:$wh = $twh;endif;

                            if($twh <= 0): $absent++; else: $present++; endif;

                            $total_wh += ($twh > 0)?round(($twh/3600),2):0;
                        else:
                            if($currentDay != 'Wed'): $absent++; endif;
                        endif;
                    endif;
                endforeach;

                $activeStructure = $this->employee->getActiveSalaryStructure($row->id);
                $empSalaryData = array();
                if(!empty($activeStructure)):
                    if(!empty($row->emp_type) && $row->emp_type == "1"):

                    else:
                        $totalEarning = 0;
                        $empSalarayHeads = (!empty($activeStructure->salary_head_json))?json_decode($activeStructure->salary_head_json):array();

                        $actual_wage = ((!empty($empSalarayHeads->actual_wage->cal_value))?$empSalarayHeads->actual_wage->cal_value:0);

                        $totalEarning = round((($actual_wage / 8) * $total_wh),0);

                        $totalDays = $absent + $present;
                        $payroll_wages = ((!empty($empSalarayHeads->payroll_wages->cal_value))?$empSalarayHeads->payroll_wages->cal_value:0);
                        
                        //print_r($totalEarning ."/". $payroll_wages." - ".$row->id);print_r("<hr>");
                        
                        $present = round(($totalEarning / $payroll_wages),0);
                        $present = ($present > $totalDays)?$totalDays:$present;
                        $absent = $totalDays - $present;
                        
                        $presentArray = explode(".",sprintf("%.2f",$present));
                        $decimalVal = $presentArray[1];
                        $diffVal = 0;
                        if($decimalVal < 50):
                            $diffVal = sprintf("%.2f",(sprintf("%.2f",$present) - sprintf("%.2f",$presentArray[0])));
                            $present = $presentArray[0];
                        else:
                            $diffVal = sprintf("%.2f",(sprintf("%.2f",$present) - sprintf("%.2f",($presentArray[0] + 0.50))));
                            $present = $presentArray[0] + 0.50;
                        endif;
                        
                        $otherAmount = 0;$other = 0;
                        if((!empty($empSalarayHeads->basic_da->cal_method) && $empSalarayHeads->basic_da->cal_method == 1)):
                            $other = ((!empty($empSalarayHeads->basic_da->cal_value))?$empSalarayHeads->basic_da->cal_value:0);
                            $otherAmount += round(($diffVal * $other),0);                            
                        else:
                            $other = ((!empty($empSalarayHeads->basic_da->cal_value))?$empSalarayHeads->basic_da->cal_value:0);
                            $other = round((($actual_wage * $other)/100),0);
                            $otherAmount += round(($diffVal * $other),0);
                        endif;     
                        
                        $basicAmount = 0;$basic = 0;
                        if((!empty($empSalarayHeads->basic_da->cal_method) && $empSalarayHeads->basic_da->cal_method == 1)):
                            $basic = ((!empty($empSalarayHeads->basic_da->cal_value))?$empSalarayHeads->basic_da->cal_value:0);
                            $basicAmount = round(($present * $basic),0);                            
                        else:
                            $basic = ((!empty($empSalarayHeads->basic_da->cal_value))?$empSalarayHeads->basic_da->cal_value:0);
                            $basic = round((($actual_wage * $basic)/100),0);
                            $basicAmount = round(($present * $basic),0);
                        endif;                        

                        $empSalaryData['basic_da']['head_name'] = "Basic ".$basic;
                        $empSalaryData['basic_da']['type'] = 1;
                        $empSalaryData['basic_da']['amount'] = $basicAmount;                        

                        $hra = 0;$hraAmount = 0;
                        if((!empty($empSalarayHeads->hra->cal_method) && $empSalarayHeads->hra->cal_method == 1)):
                            $hra = ((!empty($empSalarayHeads->hra->cal_value))?$empSalarayHeads->hra->cal_value:0);
                            $hraAmount = round($hra,0);
                        else:                            
                            $hra = ((!empty($empSalarayHeads->hra->cal_value))?$empSalarayHeads->hra->cal_value:0);
                            $hraAmount = round((($hra * $basicAmount)/100),0);
                        endif;
                        
                        $empSalaryData['hra']['head_name'] = "HRA";
                        $empSalaryData['hra']['type'] = 1;
                        $empSalaryData['hra']['amount'] = round($hraAmount,0);

                        $otherAmount += $totalEarning - $basicAmount - $hraAmount;
                        $empSalaryData['other_allowance']['head_name'] = "Other Allowance";
                        $empSalaryData['other_allowance']['type'] = 1;
                        $empSalaryData['other_allowance']['amount'] = round(($otherAmount),0);

                        /* $empSalaryData['total_earning']['head_name'] = "Total Earning";
                        $empSalaryData['total_earning']['type'] = 1;
                        $empSalaryData['total_earning']['amount'] = round(($totalEarning),0); */

                        if($row->pf_applicable == 1):
                            $pf = 0;$pfAmount = 0;
                            if((!empty($empSalarayHeads->pf->cal_method) && $empSalarayHeads->pf->cal_method == 1)):
                                $pf = ((!empty($empSalarayHeads->pf->cal_value))?$empSalarayHeads->pf->cal_value:0);
                                $pfAmount = round($pf,0);
                            else:
                                $pf = ((!empty($empSalarayHeads->pf->cal_value))?$empSalarayHeads->pf->cal_value:0);
                                $pfAmount = round(((($otherAmount + $basicAmount) * $pf)/100),0);
                            endif;
                            $empSalaryData['pf']['head_name'] = "PF";
                            $empSalaryData['pf']['type'] = -1;
                            $empSalaryData['pf']['amount'] = round(($pfAmount),0);
                        endif;

                        if($totalEarning >= 12000):
                            $empSalaryData['pt']['head_name'] = "PT";
                            $empSalaryData['pt']['type'] = -1;
                            $empSalaryData['pt']['amount'] = 200;
                        endif;

                        if(date('m',strtotime($data['month'])) == "06" || date('m',strtotime($data['month'])) == "12"):
                            $lwf = 0;$lwfAmount = 0;
                            if((!empty($empSalarayHeads->lwf->cal_method) && $empSalarayHeads->lwf->cal_method == 1)):
                                $lwf = ((!empty($empSalarayHeads->lwf->cal_value))?$empSalarayHeads->lwf->cal_value:0);
                                $lwfAmount = round($lwf,0);
                            else:                                
                                $lwf = ((!empty($empSalarayHeads->lwf->cal_value))?$empSalarayHeads->lwf->cal_value:0);
                                $lwfAmount = round((($totalEarning * $lwf)/100),0);
                            endif;

                            $empSalaryData['lwf']['head_name'] = "LEF";
                            $empSalaryData['lwf']['type'] = -1;
                            $empSalaryData['lwf']['amount'] = round(($lwfAmount),0);
                        endif;

                        $empSalaryData['canteen']['head_name'] = "Canteen";
                        $empSalaryData['canteen']['type'] = -1;
                        $empSalaryData['canteen']['amount'] = 0;

                        $empSalaryData['advance']['head_name'] = "Advance";
                        $empSalaryData['advance']['type'] = -1;
                        $empSalaryData['advance']['amount'] = 0;
                    endif;

                    $earningData = array(); $deductionData = array(); $totalEarn=0;$totalDeduction=0;$netSalary = 0;
                    foreach($empSalaryData as $key => $es):
                        if($es['type'] == 1):
                            $earningData[$key]['head_name'] = $es['head_name'];
                            $earningData[$key]['type'] = $es['type'];
                            $earningData[$key]['amount'] = $es['amount'];
                            $totalEarn += $es['amount'];
                        else:
                            $deductionData[$key]['head_name'] = $es['head_name'];
                            $deductionData[$key]['type'] = $es['type'];
                            $deductionData[$key]['amount'] = $es['amount'];
                            $totalDeduction += $es['amount'];
                        endif;
                        $netSalary += ($es['amount'] * $es['type']);
                    endforeach;

                    $dataRow = [
                        'emp_id'=>$row->id,
                        'emp_name'=>$row->emp_name,
                        'total_wh' => $total_wh,
                        'present_days' => ($present + $diffVal),
                        'working_days' => $totalDays,
                        'absent_days' => $absent,
                        'total_earning' => $totalEarn,
                        'basic_salary' => $basicAmount,
                        'earningData'=>json_encode($earningData),
                        'total_deduction' => $totalDeduction,
                        'deductionData'=>json_encode($deductionData),
                        'net_salary' => $netSalary,
                        'remark' => ""
                    ];
                    $editButton = "<button type='button' class='btn btn-outline-warning' title='Edit' onclick='Edit(".json_encode($dataRow).",this);'><i class='ti-pencil-alt'></i></button>";
                    $html .= '<tr>
                        <td>'.$sr_no.'</td>
                        <td>
                            '.$row->emp_name.'
                            <input type="hidden" name="salary_data['.$sr_no.'][emp_id]" value="'.$row->id.'">
                            <input type="hidden" name="salary_data['.$sr_no.'][emp_name]" value="'.$row->emp_name.'">
                        </td>
                        <td>
                            '.$total_wh.'
                            <input type="hidden" name="salary_data['.$sr_no.'][total_wh]" value="'.$total_wh.'">
                        </td>
                        <td>
                            '.$present.'
                            <input type="hidden" name="salary_data['.$sr_no.'][present_days]" value="'.$present.'">
                            <input type="hidden" name="salary_data['.$sr_no.'][working_days]" value="'.$totalDays.'">
                        </td>
                        <td>
                            '.$absent.'
                            <input type="hidden" name="salary_data['.$sr_no.'][absent_days]" value="'.$absent.'">
                        </td>
                        <td>
                            '.$totalEarn.'
                            <input type="hidden" name="salary_data['.$sr_no.'][total_earning]" value="'.$totalEarn.'">
                            <input type="hidden" name="salary_data['.$sr_no.'][basic_salary]" value="'.$basicAmount.'">
                            <input type="hidden" name="salary_data['.$sr_no.'][earning_data]" value="'.json_encode($earningData).'">
                        </td>
                        <td>
                            '.$totalDeduction.'
                            <input type="hidden" name="salary_data['.$sr_no.'][total_deduction]" value="'.$totalDeduction.'">
                            <input type="hidden" name="salary_data['.$sr_no.'][deduction_data]" value="'.json_encode($deductionData).'">
                        </td>
                        <td>
                            '.$netSalary.'
                            <input type="hidden" name="salary_data['.$sr_no.'][net_salary]" value="'.$netSalary.'">
                        </td>
                        <td>
                            <input type="hidden" name="salary_data['.$sr_no.'][remark]" value="">
                        </td>
                        <td>'.$editButton.'</td>
                    </tr>';
                    $sr_no++;
                endif;                
            endforeach;
        else:
            $html = '<tr>
                <td id="noData" class="text-center" colspan="10">No data available in table</td>
            </tr>';
        endif;
        $this->printJson(['status'=>1,'emp_salary_html'=>$html]);
    }
    
    public function getPayrollData($month){
        $this->data['empData'] = $this->payroll->getPayrollData($month);
        $this->load->view($this->payrollDataPage,$this->data);
    }
    public function makeSalary(){
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->payroll->save($data));
        endif;
    }

    public function edit(){
        $month = $this->input->post('month');
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->data['empData'] = $this->payroll->getPayrollData($month);
        $this->load->view($this->payrollForm,$this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->payroll->delete($id));
        endif;
    }
    
    public function getEmpSalaryJson(){
        $data = $this->input->post();
        $data['earningData'] = json_encode($data['earningData']);
        $data['deductionData'] = json_encode($data['deductionData']);
        $this->printJson(['status'=>1,'jsonData'=>$data]);
    }
}
?>