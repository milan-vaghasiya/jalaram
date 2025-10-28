<?php
class ProductionReportNew extends MY_Controller{

    private $production_report_page = "report/production_new/index";
    private $job_wise_production = "report/production_new/job_production";
    private $jobwork_register = "report/production_new/jobwork_register";
    private $production_analysis = "report/production_new/production_analysis";
    private $stage_production = "report/production_new/stage_production";
    private $jobcard_register = "report/production_new/jobcard_register";
    private $machinewise_production = "report/production_new/machinewise_production";
    private $general_oee = "report/production_new/general_oee";
    private $operator_monitor = "report/production_new/operator_monitor";
    private $rejection_monitoring = "report/production_new/rejection_monitoring";
    private $operator_performance = "report/production_new/operator_performance";
    private $production_bom = "report/production_new/production_bom";
    private $rm_planing = "report/production_new/rm_planing";
    private $fg_tracking = "report/production_new/fg_tracking";
    private $operator_wise_production = "report/production_new/operator_wise_production";
    private $daily_oee = "report/production_new/daily_oee";
    private $vendor_tracking = "report/production_new/vendor_tracking";
    private $fg_planing = "report/production_new/fg_planing";
    private $job_costing = "report/production_new/job_costing";
    private $jobcard_wise_costing = "report/production_new/jobcard_wise_costing";
    private $job_material_tracking = "report/production_new/job_material_tracking";
    private $order_monitor = "report/production_new/order_monitor";
    private $material_requirements_planning = "report/production_new/material_requirements_planning";
    private $machine_log_summary = "report/production_new/machine_log_summary";
    private $costing_report = "report/production_new/costing_report";
    private $job_costing_report = "report/production_new/job_costing_report";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Production Report";
        $this->data['headData']->controller = "reports/productionReportNew";
        $this->data['floatingMenu'] = '';//$this->load->view('report/production_new/floating_menu', [], true);
    }

    public function index()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'PRODUCTION REPORT';
        $this->load->view($this->production_report_page, $this->data);
    }

    /* Job Wise Production */
    public function jobProduction($item_id = "")
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOB WISE PRODUCTION';
        $this->data['jobcardData'] = $this->productionReportsNew->getJobcardList();
        $this->data['itemId'] = $item_id;
        $this->load->view($this->job_wise_production, $this->data);
    }

    public function getJobWiseProduction()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getJobWiseProduction($data);
        $this->printJson($result);
    }

    /* Jobwork Register */
    public function jobworkRegister()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOB WORK OUTWARD-INWARD REGISTER';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->jobwork_register, $this->data);
    }

    public function getJobworkRegister()
    {
        $data = $this->input->post();
        $data['prod_type'] = 1;
        $jobOutData = $this->productionReportsNew->getJobworkRegister($data);

        $blankInTd = '<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $k = 1;
        $tblData = ""; $totalOutPsc = 0; $totalOutKgs = 0; $totalInPsc = 0; $totalInKgs = 0; $totalBalancePsc = 0; $totalBalanceKgs = 0;
        foreach ($jobOutData as $row) :
           
            $pids = explode(',', $row->process);
            $idx = 0;
            $md = "";
            foreach ($pids as $key => $pid) :
                if ($pid == $row->process_id) :
                    $idx = $key;
                endif;
            endforeach;
            $uname = $row->punit;
            $outQty = 0;
            $rmFlag = false;
            $umData = $this->productionReportsNew->getUsedMaterialByJobCardId($row->job_card_id);
            if ($idx == 0) :
                $outQty = (!empty($umData->issue_qty))?$umData->issue_qty:0;
                $md = "Raw Material";
                $uname = (!empty($umData->unit_name))?$umData->unit_name:'';
                $rmFlag = true;
                $batch_no = (!empty($umData->batch_no))?$umData->batch_no:'';
            else :
                $outQty = $row->qty;
                $md = $this->process->getProcess($pids[$idx - 1])->process_name . ' Ok';
                $uname = $row->punit;
                $batch_no = (!empty($umData->batch_no))?$umData->batch_no:'';
            endif;
            //Caret Details @AAVRUTI
            //  $jobChallanData = $this->productionReportsNew->getJobChallan($row->id);
            
            $materialData = (!empty($row->material_data) ? json_decode($row->material_data) : "");
            $outwardDetails = "";
            $inwardDetails = "";
            if (!empty($materialData)) : $i = 1;
                foreach ($materialData as $chData) :
                    if(!empty($chData->item_id)){
                        $item_name = $this->item->getItem($chData->item_id)->item_name;
                        if ($i == 1) {
                            $outwardDetails .= 'Plastic Crate (Qty:. ' . $chData->out_qty . ')';
                            $inwardDetails .= 'Plastic Crate (Qty:. ' . $chData->in_qty . ')';
                        } else {
                            $outwardDetails .= '<br> Plastic Crate (Qty:. ' . $chData->out_qty . ')';
                            $inwardDetails .= '<br> Plastic Crate (Qty:. ' . $chData->in_qty . ')';
                        }
                        $i++;
                    }
                endforeach;
            endif;
            
            $kgQty = 0;
            $kgQty = (!empty($row->w_pcs) && $row->w_pcs > 0)?floatVal($row->w_pcs * $row->qty):0;
        
            $totalOutPsc += floatVal($row->qty); $totalOutKgs += $kgQty;
            $tblData .= '<tr>
                            <td>' . $k++ . '</td>
                            <td>' . formatDate($row->trans_date) .'</td>
                            <td>' . (!empty($row->jwo_no)?(getPrefixNumber($row->jwo_prefix,$row->jwo_no)):""). '</td>
                            <td>' . (!empty($row->trans_number)?($row->trans_number):''). '</td>
                            <td>' . $row->item_code . '</td>
                            <td>' . $md . '</td>
                            <td>' . $row->process_name . '</td>
                            <td>' . floatVal($row->qty) . '</td>
                            <td>' . (($kgQty > 0)?$kgQty:"-") . '</td>
                            <td>' . $batch_no . '</td>
                            <td>' . $outwardDetails . '</td>';
                            
            $inData = $this->productionReportsNew->getJobworkRegister(['ref_id'=>$row->id,'prod_type'=>2,'from_date'=>$data['from_date'],'to_date'=>$data['to_date']]);

            $inCount = count($inData); 
            if ($inCount > 0) :
                $usedQty = 0;
                $j = 1;
                foreach ($inData as $inRow) :
                    $balQty = 0;                    
                    if($rmFlag) :
                        $outQty = (!empty($umData->wp_qty))?($umData->wp_qty * $row->qty):$row->qty; //0.165 * 23 = 3.795
                        $usedQty += (!empty($umData->wp_qty))?($umData->wp_qty * $inRow->qty):$inRow->qty;
                    else:
                        $usedQty += $inRow->qty + $inRow->rej_qty;
                    endif; 
                    
                    $balQty = round((floatVal($outQty) - floatVal($usedQty)),2);
                    
                    $inkgQty = 0;
                    $inkgQty = (!empty($inRow->w_pcs) && $inRow->w_pcs > 0)?floatVal($inRow->w_pcs * floatVal($inRow->qty)):0;
                    $balanceKg = (!empty($inRow->w_pcs) && $inRow->w_pcs > 0)?floatVal($inRow->w_pcs * floatVal($balQty)):0;
                  
                    $totalInPsc += floatVal($inRow->qty); $totalInKgs += $inkgQty; $totalBalancePsc += $balQty; $totalBalanceKgs += $balanceKg;
                    $vendor = (!empty($inRow->vendor_name)) ? $inRow->vendor_name : 'In House';
                    $tblData .= '<td>' . formatDate($inRow->in_challan_date) . '</td>
                                    <td>' . $vendor . '</td>
                                    <td>' . $inRow->item_code . '</td>
                                    <td>' . (!empty($row->trans_number)?($row->trans_number):''). '</td>
                                    <td>' . $inRow->in_challan_no . '</td>
                                    <td>' . floatVal($inRow->qty) . '</td>
                                    <td>' . (($inkgQty > 0)?$inkgQty:"-") . '</td>
                                    <td>' . floatval($balQty) . '</td>
                                    <td>' . ((floatval($balanceKg) > 0)?floatval($balanceKg):"-") . '</td>
                                    <td>' . floatVal($inRow->rej_qty) . '</td>
                                    <td>' . $batch_no . '</td>';
                    if($j==1){
                        $tblData.='<td>' . $inwardDetails . '</td>';
                    }else{
                        $tblData.='<td>-</td>';
                    }             
                    if ($j != $inCount) {
                        $tblData .= '</tr><tr><td>-</td>' . $blankInTd;
                    }
                    $j++;
                endforeach;
            else :
                $tblData .= '<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>';
            endif;
            $tblData .= '</tr>';
        endforeach;
        $this->printJson(['status' => 1, "tblData" => $tblData,'totalOutPsc' => $totalOutPsc, 'totalOutKgs' => $totalOutKgs, 'totalInPsc' => $totalInPsc, 'totalInKgs' => $totalInKgs, 'totalBalancePsc' => $totalBalancePsc, 'totalBalanceKgs' => $totalBalanceKgs]);
    }
    
    /* Machine Wise Production OEE Report */
    public function machineWise()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'MACHINE WISE OEE REGISTER';
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->load->view($this->machinewise_production, $this->data);
    }

    public function getMachineData()
    {
        $id = $this->input->post('dept_id');
        $machineData = $this->productionReportsNew->getDepartmentWiseMachine($id);
        $option = '<option value="">Select Machine</option>';
        foreach ($machineData as $row) :
            $option .= '<option value="' . $row->id . '" >[' . $row->item_code . '] ' . $row->item_name . '</option>';
        endforeach;

        $this->printJson(['status' => 1, 'option' => $option]);
    }

    public function getMachineWiseProduction()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getMachineWiseProduction($data, $data['dept_id']);
            $i = 1;
            $tbody = "";
            $mcData = $this->item->getItem($data['machine_id']);
            $totalProdTime = 0;
            $totalPlanQty = 0;
            $totalRunTime = 0;
            $totalProduction = 0;
            $breakDownTime = 0;
            $avgAvailability = 0;
            $avgPerformance = 0;
            $avgOee = 0;
            foreach ($productionData as $row) :
                $idlData = $this->productionReportsNew->getIdleTimeReasonMachineWise($row->log_date, $row->shift_id, $data['machine_id']);

                $plan_time = !empty($row->shift_hour)?$row->shift_hour:660;
                $ct = (!empty($row->m_ct)) ? ($row->m_ct) : 0;//print_r($plan_time.'-'.($idlData['breakdown_time']/60));
                $runTime = round(($plan_time - ($idlData['setting_time'] / 60)-($idlData['breakdown_time']/60) - (($row->load_unload_time * $row->ok_qty) / 60)), 2);
                $plan_qty = (!empty($runTime) && !empty($ct)) ? (($runTime * 60) / $ct) : 0;
                $availability = (!empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                $performance = (!empty($plan_qty)) ? (($row->ok_qty * 100) / $plan_qty) : 0;
                $oee = ($availability + $performance) / 2;

                $totalProdTime +=$plan_time;
                $totalPlanQty +=$plan_qty;
                $totalRunTime +=$runTime;
                $totalProduction +=$row->ok_qty;
                $breakDownTime +=$row->idle_time;
                $avgAvailability +=$availability;
                $avgPerformance += $performance;
                $avgOee +=$oee;
                $tbody .= '<tr class="text-center">
										<td>' . $i++ . '</td>
										<td>' . formatDate($row->log_date) . '</td>
										<td>' . $row->shift_name . '</td>
                                        <td>'.$row->product_code.'</td>
                                        <td>'.$row->process_name.'</td>
										<td>' . $plan_time . '</td>
										<td>' . (int)$plan_qty . '</td>
										<td>' . $runTime . '</td>
										<td>' . $row->ok_qty . '</td>
										<td>' . $row->idle_time . '</td>
                                        ' . $idlData['td'] . '
                                        <td>' . number_format($availability) . '%</td>
										<td>' . number_format($performance) . '%</td>
										<td>' . number_format($oee) . '%</td>
				</tr>';

            endforeach;
            $td = $this->productionReportsNew->getTotaIdleTimeReasonForOee($data['from_date'],$data['to_date'],$data['machine_id']);
            $totalRecords=count($productionData);
            $tfoot = '<tr class="text-center" style="font-weight : bold" >
										<td colspan="5" class="text-right" >Total</td>
										<td >' . number_format($totalProdTime/60,2). '</td>
										<td >' . (int)$totalPlanQty . '</td>
										<td >' . number_format($totalRunTime/60,2) . '</td>
										<td >' . $totalProduction . '</td>
										<td >' . number_format($breakDownTime/60,2) . '</td>
                                        ' . $td . '
                                        <td >' . (!empty($totalRecords)?number_format($avgAvailability/$totalRecords):0) . '%</td>
										<td >' . (!empty($totalRecords)?number_format($avgPerformance/$totalRecords):0) . '%</td>
										<td >' .(!empty($totalRecords)?number_format($avgOee/$totalRecords):0)  . '%</td>
								</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    /* Operator Wise OEE */
    public function operatorWiseOee()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'OPERATOR WISE OEE REGISTER';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->load->view($this->operator_wise_production, $this->data);
    }

    public function getOperatorWiseProduction()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getOperatorWiseProduction($data);
            $i = 1;
            $tbody = "";
            $totalPlanTime = 0;
            $totalPlanQty = 0;
            $totalRunTime = 0;
            $totalOkQty = 0;
            $totalBreakDownTime = 0;
            $avgAvailabitlity = 0;
            $avgPerformance = 0;
            $avgOEE = 0;
            $operator = (!empty($data['operator_id'])) ? $this->employee->getEmp($data['operator_id'])->emp_name : 'NO OPERATOR';
            foreach ($productionData as $row) :
                $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                //changed by mansee
                $performanceTime = $plan_time - $row->idle_time;
                $ct = (!empty($row->m_ct)) ? ($row->m_ct) : 0;
                $total_load_unload_time = ($row->load_unload_time * $row->production_qty) / 60; //changed by mansee
                $runTime = $plan_time - $row->idle_time - $total_load_unload_time; //changed by mansee

                $plan_qty = (!empty($runTime) && !empty($ct)) ? (($runTime * 60) / $ct) : 0;
                $availability = (!empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                // $performance = (!empty($plan_qty)) ? (($row->ok_qty * 100) / $plan_qty) : 0;
                //changed by mansee
                if (!empty($performanceTime)) {
                    $performance = (!empty($row->cycle_time)) ? (((($row->cycle_time + $row->load_unload_time) * $row->ok_qty) / ($performanceTime)) / 60) * 100 : 0;
                } else {
                    $performance = 0;
                }
                // $oee = ($availability + $performance) / 2;//changed by mansee $opData->emp_name

                $oee = (($availability / 100) * ($performance / 100)) * 100;

                $totalPlanTime += $plan_time;
                $totalPlanQty += $plan_qty;
                $totalRunTime += $runTime;
                $totalOkQty += $row->ok_qty;
                $totalBreakDownTime += $row->idle_time;
                $avgAvailabitlity += $availability;
                $avgPerformance += $performance;
                $avgOEE += $oee;
                $tbody .= '<tr class="text-center">
										<td>' . $i++ . '</td>
										<td>' . formatDate($row->log_date) . '</td>
										<td>' . $row->shift_name . '</td>
										<td>' . $row->product_code . '</td>
										<td>' . $row->process_name . '</td>
										<td>' . $plan_time . '</td>
										<td>' . (int)$plan_qty . '</td>
										<td>' . number_format($runTime, 2) . '</td>
										<td>' . $row->ok_qty . '</td>
										<td>' . $row->idle_time . '</td>
                                        <td>' . number_format($availability, 2) . '%</td>
										<td>' . number_format($performance, 2) . '%</td>
										<td>' . number_format($oee, 2) . '%</td>
								</tr>';
            endforeach;
            $totalRecords = count($productionData);
            $tfoot = '<tr class="text-center" style="font-weight : bold">
            <td colspan="5" class="text-right">Total</td>
            <td>' . number_format(($totalPlanTime / 60),2) . '</td>
            <td>' . (int)$totalPlanQty . '</td>
            <td>' . number_format($totalRunTime / 60, 2) . '</td>
            <td>' . $totalOkQty . '</td>
            <td>' . number_format(($totalBreakDownTime / 60),2) . '</td>
            <td>' . (!empty($totalRecords) ? number_format($avgAvailabitlity / $totalRecords, 2) : 0) . '%</td>
            <td>' . (!empty($totalRecords) ? number_format($avgPerformance / $totalRecords, 2) : 0) . '%</td>
            <td>' . (!empty($totalRecords) ? number_format($avgOEE / $totalRecords, 2) : 0) . '%</td>
            </tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }

    /* OEE Register */
    public function oeeRegister()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'OEE REGISTER';
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->load->view($this->general_oee, $this->data);
    }

    public function getOeeData()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getOeeData($data);
            $i = 1;
            $tbody = "";
            $totalProduction = 0;
            $totalRejQty = 0;
            $totalRwQty = 0;
            $totalIdleTime = 0;
            $planProdTime = 0;
            $totalRunTime = 0;
            $totalPlanQty = 0;
            $totalOkQty = 0;
            $totalLUTime = 0;
            $avgAvailability = array();
            $avgOverPerformance = array();
            $avgPerformance = array();
            $avgQualityRate = array();
            $avgQEE = array();
            foreach ($productionData as $row) :
                $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                $performanceTime = $plan_time - $row->idle_time;
                $ct = (!empty($row->m_ct)) ? ($row->m_ct / 60) : 0;
                $total_load_unload_time = ($row->total_load_unload_time * $row->production_qty) / 60;
                $runTime = $plan_time - $row->idle_time - $total_load_unload_time;
                $plan_qty = (!empty($runTime) && !empty($ct)) ? ($runTime / $ct) : 0;
                $availability = ($plan_time > 0 && !empty($runTime) && !empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                if (!empty($performanceTime)) {
                    $performance = (!empty($row->cycle_time)) ? (((($row->cycle_time + $row->total_load_unload_time) * $row->production_qty) / ($performanceTime)) / 60) * 100 : 0;
                } else {
                    $performance = 0;
                }
                $row->ok_qty = ($row->ok_qty > 0)?$row->ok_qty:0;
                $overall_performance = (!empty($row->cycle_time) && !empty($plan_time)) ? ((((($row->cycle_time + $row->total_load_unload_time) / 60) * $row->production_qty) / $plan_time)) * 100 : 0;
                $quality_rate = ($row->production_qty > 0) ? round((($row->ok_qty * 100) / $row->production_qty),2) : 0;
                $oee = (($availability / 100) * ($performance / 100) * ($quality_rate / 100)) * 100;
                $td = $this->productionReportsNew->getIdleTimeReasonForOee($row->log_date, $row->shift_id, $row->machine_id, $row->process_id, $row->operator_id, $row->product_id);
                $totalProduction += $row->production_qty;
                $totalRejQty += $row->rej_qty;
                $totalRwQty += $row->rw_qty;
                $totalIdleTime += $row->idle_time;
                $planProdTime += $plan_time;
                $totalRunTime += $runTime;
                $totalPlanQty += $plan_qty;
                $totalOkQty += $row->ok_qty;
                $totalLUTime += $total_load_unload_time;
                
                if($availability > 0): $avgAvailability[] = $availability; endif;
                if($overall_performance > 0): $avgOverPerformance[] = $overall_performance; endif;
                if($performance > 0): $avgPerformance[] = $performance; endif;
                if($quality_rate > 0): $avgQualityRate[] = $quality_rate; endif;
                if($oee > 0): $avgQEE[] = $oee; endif;
                
                $tbody .= '<tr class="text-center">
					<td>' . $i++ . '</td>
					<td>' . formatDate($row->log_date) . '</td>
					<td>' . $row->shift_name . '</td>
					<td>' . $row->item_code . '</td>
					<td>' . $row->machine_code . '</td>
					<td>' . $row->emp_name . '</td>
					<td>' . $row->process_name . '</td>
					<td>' . $row->cycle_time . '</td>
					<td>' . $row->total_load_unload_time . '</td>
					<td>' . $row->production_qty . '</td>
					<td>' . $row->rej_qty . '</td>
					<td>' . $row->rw_qty . '</td>
					<td>' . $row->idle_time . '</td>
                    ' . $td . '
					<td>' . $plan_time . '</td>
					<td>' . number_format($runTime, 2) . '</td>
					<td>' . (int)$plan_qty . '</td>
					<td>' . $row->ok_qty . '</td>
					<td>' . number_format($total_load_unload_time, 2) . '</td>
                    <td>' . number_format($availability, 2) . '%</td>
                    <td>' . number_format($overall_performance, 2) . '%</td>
					<td>' . number_format($performance, 2) . '%</td>
					<td>' . number_format($quality_rate, 2) . '%</td>
					<td>' . number_format($oee, 2) . '%</td>
    			</tr>';
            endforeach;
            
            $avg_qc_rate = ($totalProduction > 0) ? round((($totalOkQty * 100) / $totalProduction),2) : 0;
            $td = $this->productionReportsNew->getTotaIdleTimeReasonForOee($data['from_date'], $data['to_date']);
            $totalRecord = count($productionData);
            $tfoot = '<tr class="text-center" style="font-weight : bold">
            <td colspan="9" class="text-right">Total</td>
            <td>' . $totalProduction . '</td>
            <td>' . $totalRejQty . '</td>
            <td>' . $totalRwQty . '</td>
            <td>' . number_format($totalIdleTime / 60, 2) . '</td>
            ' . $td . '
            <td>' . ($planProdTime / 60) . '</td>
            <td>' . number_format($totalRunTime / 60, 2) . '</td>
            <td>' . (int)$totalPlanQty . '</td>
            <td>' . $totalOkQty . '</td>
            <td>' . (($totalLUTime > 0) ? number_format($totalLUTime / 60, 2) : 0) . '</td>
            <td>' . ((count($avgAvailability) > 0) ? number_format((array_sum($avgAvailability)/count($avgAvailability)), 2) : 0) . '%</td>
            <td>' . ((count($avgOverPerformance)) ? number_format((array_sum($avgOverPerformance)/count($avgOverPerformance)), 2) : 0) . '%</td>
            <td>' . ((count($avgPerformance)) ? number_format((array_sum($avgPerformance)/count($avgPerformance)), 2) : 0) . '%</td>
            <td>' . $avg_qc_rate . '%</td>
            <td>' . ((count($avgQEE)) ? number_format((array_sum($avgQEE)/count($avgQEE)), 2) : 0) . '%</td>
            </tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }

    /* stage wise Production */
    public function stageProduction()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'STAGE WISE PRODUCTION';
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->stage_production, $this->data);
    }

    public function getStageWiseProduction()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $stageData = $this->productionReportsNew->getStageWiseProduction($data);
            $jobData = $stageData['jobData'];
            $processList = $stageData['processList'];

            $thead = '';
            $tbody = "";
            if (!empty($processList)) :
                $thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th>';
                $l = 0;
                $p = 0;
                foreach ($jobData as $row) :
                    $qtyTD = '';
                    foreach ($processList as $pid) :
                    $qty = 0;
                        $pcData = $this->process->getProcessDetail($pid);
                        $process_name = (!empty($pcData)) ? $pcData->process_name : "";
                        if ($l == 0) {
                            $thead .= '<th>' . $process_name . '<br>(Ok Qty.)</th>';
                    }
                    if (in_array($pid, explode(',', $row->process))) :
                        $qty = $this->productionReportsNew->getProductionQty($row->id, $pid)->qty;
                    endif;
                    $qtyTD .= (!empty($qty)) ? '<td>' . floatVal($qty) . '</td>' : '<td>-</td>';
                    endforeach;
                //Packed Qty.
                $stockQry = [
                    'item_id' => $row->product_id,
                    'batch_no' => getPrefixNumber($row->job_prefix, $row->job_no)
                ];
                $stock = $this->store->getJobwisePackInv($stockQry);
                $rtd = $this->store->getItemStockRTD($row->product_id,1);
                    if ($l == 0) {
                        $thead .= '<th>Packing <br>Area</th><th>RTD</th>';
                    }
                    $inv_qty = (!empty($stock) && !empty($stock->inv_qty)) ? abs($stock->inv_qty) : '0';
                    $pack_qty = (!empty($stock) && !empty($stock->packing_qty)) ? floatVal($stock->packing_qty) : '0';
                    $qtyTD .= '<td>' . (($pack_qty <> 0) ? $pack_qty : '-') . '</td>';
                    $qtyTD .= '<td>' . (($rtd->qty <> 0) ? floatVal($rtd->qty) : '-') . '</td>';
                    $tbody .= '<tr class="text-center">
                                <td>' . getPrefixNumber($row->job_prefix, $row->job_no) . '</td>
                                <td>' . $row->item_code . '</td>
                                <td>' . floatVal($row->job_qty) . '</td>
                                ' . $qtyTD . '
                            </tr>';
                    $l++;
                endforeach;
            else :
                $thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th><th style="min-width:100px;">Process List</th></tr>';
            endif;


            $this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody]);

        endif;
    }
 
    /* Jobcard Register */
    public function jobcardRegister()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOB CARD REGISTER';
        
        $data = $this->input->post();
        $postData = [];
        if(empty($data)){
            $postData['from_date'] = date('Y-m-01');
            $postData['to_date'] = date('Y-m-t');
        }else{
            $postData['from_date'] = $data['from_date'];
            $postData['to_date'] = $data['to_date'];
        }
        
        $jobCardData = $this->productionReportsNew->getJobcardRegister($postData);
        
        $html = ''; $i = 1;
        foreach ($jobCardData as $row) :
            $cname = !empty($row->party_code) ? $row->party_code : "Self Stock";
            $qtyData = $this->productionReportsNew->getJobRejData($row->id);
            $html .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . getPrefixNumber($row->job_prefix, $row->job_no) . '</td>
                <td>' . formatDate($row->job_date) . '</td>
                <td>' . $row->item_code . '</td>
                <td>' . $cname . '</td>
                <td>' . $row->challan_no . '</td>
                <td>' . $row->heat_no . '</td>
                <td>' . $row->total_weight . '</td>
                <td>' . floatVal($row->qty) . '</td>
                <td>' . floatVal($row->total_ok_qty) . '</td>
                <td>' . floatVal($qtyData->rejection_qty) . '</td>
                <td>' . floatVal($row->qty - $row->total_ok_qty) . '</td>
                <td>' . $row->emp_name . '</td>
                <td>' . $row->remark . '</td>
            </tr>';
        endforeach;
        
        if(empty($data)){
            $this->data['jobRegHtml'] = $html;
            $this->load->view($this->jobcard_register, $this->data);
        }else{
            $this->printJson(['status' => 1, 'tbody' => $html]);
        }
    }

     /* Operator Monitoring */
    public function operatorMonitoring()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'OPERATOR MONITORING';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->load->view($this->operator_monitor, $this->data);
    }

    public function getOperatorMonitoring()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getOperatorMonitoring($data);
            $i = 1; $tbody = ""; $thead = "";
            $thead .= '<tr><th style="min-width:50px;">#</th>
                            <th style="min-width:100px;">Date</th>
                            <th style="min-width:80px;">Shift</th>
                            <th style="min-width:80px;">M/C No.</th>
                            <th style="min-width:100px;">Part No.</th>
                            <th style="min-width:150px;">Setup</th>';
            if($data['type'] == 1):
                $thead .= '<th style="min-width:50px;">Cycle Time(m:s)</th>
                            <th style="min-width:50px;">Production Time(h:m)</th>
                            <th style="min-width:100px;">Ok Qty.</th>';                
            elseif($data['type'] == 2):
                $thead .= ' <th style="min-width:50px;">R/w. Qty.</th>
                            <th style="min-width:50px;">Rej. Qty.</th>';
            else:
                $thead .= ' <th style="min-width:50px;">Cycle Time(m:s)</th>
                            <th style="min-width:50px;">Production Time(h:m)</th>
                            <th style="min-width:100px;">Ok Qty.</th>
                            <th style="min-width:50px;">R/w. Qty.</th>
                            <th style="min-width:50px;">Rej. Qty.</th>';
            endif;

            foreach ($productionData as $row) :
                    $tbody .= '<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . formatDate($row->log_date) . '</td>
                                <td>' . $row->shift_name . '</td>
                                <td>' . $row->machine_no . '</td>
                                <td>' . $row->item_code . '</td>
                                <td>' . $row->process_name . '</td>';
                    if($data['type'] == 1):
                        $tbody .= '<td>' . $row->cycle_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td>' . $row->ok_qty . '</td>';
                    elseif($data['type'] == 2):
                        $tbody .= '<td>' . $row->rw_qty . '</td>
                                <td>' . $row->rej_qty . '</td>';
                    else:
                        $tbody .= '<td>' . $row->cycle_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td>' . $row->ok_qty . '</td>
                                <td>' . $row->rw_qty . '</td>
                                <td>' . $row->rej_qty . '</td>';
                    endif;
                    $tbody .= '</tr>';     
            endforeach;
            $thead .= '</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead]);
        endif;
    }

    /* Rejection Monitoring  avruti*/
    public function rejectionMonitoring()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'REJECTION & REWORK MONITORING REPORT';
        $this->data['itemDataList'] = $this->item->getItemList(1);
        $this->load->view($this->rejection_monitoring, $this->data);
    }

    public function getRejectionMonitoring()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $rejectionData = $this->productionReports->getRejectionMonitoring($data);
            $this->printJson($rejectionData);
        endif;
    }

    /*Production Bom Created By Meghavi 1-1-22*/
    public function productionBom()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Production Bom Report';
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['refItemData'] = $this->item->getItemList(3);
        $this->load->view($this->production_bom, $this->data);
    }

    public function getItemBomData()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getItemWiseBom($data);
        $i = 1;
		$tbody = "";
		foreach ($result as $row) :
			$tbody .= '<tr class="text-center">
						<td>' . $i++ . '</td>
						<td>' . $row->item_name . '</td>
						<td>' . $row->item_code . '</td>
						<td>' . $row->qty . '</td>
					</tr>';
		endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function getProductionBomData()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getProductionBomData($data);
        $i = 1; $tbody = "";
		foreach ($result as $row) : 
			$tbody .= '<tr class="text-center">
						<td>' . $i++ . '</td>
						<td>' . $row->item_name . '</td>
						<td>' . $row->item_code . '</td>
						<td>' . $row->qty . '</td>
					</tr>';
		endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function rmPlaning()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'RM PLANING';
        $this->data['rmData'] = $this->item->getItemLists(3);
        $this->load->view($this->rm_planing, $this->data);
    }

    public function getRmPlaning()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getRmPlaning($data);

        $i = 1;
        $tbody = "";
        $theadVal = '';
        if (!empty($result)) :
            foreach ($result as $row) :
                $theadVal = $row->ref_qty . ' (' . $row->uname . ')';
                $tbody .= '<tr class="text-center">
                            <td>' . $i++ . '</td>
                            <td>' . $row->item_code . ' [' . $row->item_name . ']</td>
                            <td>' . $row->qty . ' (' . $row->uname . ')</td>
                            <td>' . floor($row->ref_qty / $row->qty) . '  (' . $row->unit_name . ')</td>
                        </tr>';
            endforeach;
        endif;
        $thead = '<tr>
            <th colspan="4">RM Stock : ' . $theadVal . '</th>
        </tr>
        <tr>
            <th>#</th>
            <th>Finish Goods</th>
            <th>Bom Qty.</th>
            <th>Expected Ready Qty.</th>
        </tr>';
        $this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody]);
    }

    public function fgTracking()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'FG Tracking';
        $this->data['fgData'] = $this->item->getItemList(1);
        $this->load->view($this->fg_tracking, $this->data);
    }
    
    public function getFGStockDetail()
    {
        $data = $this->input->post();
        $i = 1;
        $tbody = "";
        $locationData = $this->store->getStoreLocationList();
        if (!empty($locationData)) {
            $prd_qty = 0;
            foreach ($locationData as $lData) {
                foreach ($lData['location'] as $batch) :
                    $result = $this->productionReportsNew->getStockTrans($data['item_id'], $batch->id);
                    if (!empty($result)) {
                        foreach ($result as $row) {
                            if ($row->stock_qty > 0) :
                                if ($row->location_id == $this->PROD_STORE->id && $row->ref_type == 7) {
                                    $prd_qty += $row->stock_qty;
                                }
                                $tbody .= '<tr>';
                                $tbody .= '<td class="text-center">' . $i . '</td>';
                                $tbody .= '<td>[' . $lData['store_name'] . '] ' . $batch->location . '</td>';
                                $tbody .= '<td>' . $row->batch_no . '</td>';
                                $tbody .= '<td>' . floatVal($row->stock_qty) . '</td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
                        }
                    }
                endforeach;
            }
        } else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        
        $wipHtml='';$totalWIPQty =0;
        $wipData = $this->productionReportsNew->getWIPStockData($data['item_id']);
        if(!empty($wipData)){
            $i=1;
            foreach($wipData as $row){
                $wipHtml.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.(!empty($row->process_name)?$row->process_name:'Raw Material').'</td>
                    <td>'.floatVal($row->wip_qty).'</td>
                </tr>';
                $totalWIPQty += $row->wip_qty;
            }
        }
        
        $this->printJson(['status' => 1, 'tbody' => $tbody,'wipHTML'=>$wipHtml,'total_wip_qty'=>$totalWIPQty]);
    }
    
    public function getFGStockDetail_old()
    {
        $data = $this->input->post();
        $i = 1;
        $tbody = "";
        $locationData = $this->store->getStoreLocationList();
        if (!empty($locationData)) {
            $prd_qty = 0;
            foreach ($locationData as $lData) {
                foreach ($lData['location'] as $batch) :
                    $result = $this->productionReports->getStockTrans($data['item_id'], $batch->id);
                    if (!empty($result)) {
                        foreach ($result as $row) {
                            if ($row->stock_qty > 0) :
                                if ($row->location_id == $this->PROD_STORE->id && $row->ref_type == 7) {
                                    $prd_qty += $row->stock_qty;
                                }
                                $tbody .= '<tr>';
                                $tbody .= '<td class="text-center">' . $i . '</td>';
                                $tbody .= '<td>[' . $lData['store_name'] . '] ' . $batch->location . '</td>';
                                $tbody .= '<td>' . $row->batch_no . '</td>';
                                $tbody .= '<td>' . floatVal($row->stock_qty) . '</td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
                        }
                    }
                endforeach;
            }
            $jobData = $this->productionReports->getJobcardWIPQty($data['item_id']);
            $wipQty = (!empty($jobData->qty) ? $jobData->qty : 0) -  $prd_qty;
            $tbody .= '<tr>';
            $tbody .= '<td class="text-center">' . $i . '</td>';
            $tbody .= '<td> WIP </td>';
            $tbody .= '<td> - </td>';
            $tbody .= '<td>' . floatVal($wipQty) . '</td>';
            $tbody .= '</tr>';
        } else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    /* Daily OEE Register */
    public function dailyOeeRegister()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Daily OEE REGISTER';
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->load->view($this->daily_oee, $this->data);
    }

    public function getDailyOeeData()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['fromDate']))
            $errorMessage['fromDate'] = "From Date is required.";
        if (empty($data['date']))
            $errorMessage['date'] = "Date is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $deptData = $this->department->getMachiningDepartment(8);
            $tbody = "";
            $i = 1;$deptCount=0;
            $totalAvailability = 0;
            $totalOverallPerformance = 0;
            $totalPerformance = 0;
            $totalQualityRate = 0;
            $totalOee = 0;
            $totalLossHours = 0;
            $totalLossHoursINR = 0;
            $totalLoadUnloadTime = 0;
            $totalProductionQty = 0;
            $totalOkQty=0;
            foreach ($deptData as $dept) {
                if($dept->id != 8):
                    $data['dept_id'] = $dept->id;
                    $productionData = $this->productionReportsNew->getDepartmentWiseOee($data);
                    $total_availability = 0;
                    $total_performance = 0;
                    $total_overall_performance = 0;
                    $total_oee = 0;
                    $total_production_qty = 0;
                    $total_ok_qty=0;
                    $total_load_unload_time = 0;
                    $total_quality_rate = 0;
                    $idleTime = 0;
                    $count = 0;
    
                    if (!empty($productionData)) {
                        $td = $this->productionReportsNew->getIdleTimeReasonForDailyOee($data['fromDate'], $data['date'], $dept->id);
                        foreach ($productionData as $row) {
                            $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                            $performanceTime = $plan_time - $row->idle_time;
                            $ct = (!empty($row->m_ct)) ? ($row->m_ct / 60) : 0;
                            $total_load_unload_time += ($row->total_load_unload_time * $row->production_qty) / 60;
                            $runTime = $plan_time - $row->idle_time - (($row->total_load_unload_time * $row->production_qty) / 60);
                            $plan_qty = (!empty($runTime) && !empty($ct)) ? ($runTime / $ct) : 0;
                            $availability = (!empty($runTime) && !empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                            $total_availability += $availability;
                            if (!empty($performanceTime)) {
                                $performance = (!empty($row->cycle_time) && !empty($plan_time)) ? (((($row->cycle_time + $row->load_unload_time) * $row->production_qty) / ($performanceTime)) / 60) * 100 : 0;
                            } else {
                                $performance = 0;
                            }
                            $total_performance += $performance;
                            $overall_performance = (!empty($row->cycle_time) && !empty($plan_time)) ? ((((($row->cycle_time + $row->load_unload_time) / 60) * $row->production_qty) / $plan_time)) * 100 : 0;
                            $total_overall_performance += $overall_performance;
                            $quality_rate = (!empty($row->production_qty)) ? $row->ok_qty * 100 / $row->production_qty : 0;
                            $total_quality_rate += $quality_rate;
                            $oee = (($availability / 100) * ($performance / 100) * ($quality_rate / 100)) * 100;
                            $total_oee += $oee;
                            $total_production_qty += $row->production_qty;
                            $total_ok_qty += $row->ok_qty;
                            $idleTime += $row->idle_time;
                            $count++;
                        }
                        
                        $avg_qcr = ($total_production_qty > 0) ? round((($total_ok_qty * 100) / $total_production_qty),2) : 0;
                        
                        $toee = round((((($total_availability/$count) / 100) * (($total_performance/$count) / 100) * ($avg_qcr / 100)) * 100),2);
                        $deptName = (!empty($dept->alias_name)) ? $dept->alias_name : $dept->name;
                        $tbody .= '<tr class="text-center">
                        <td>' . $i++ . '</td>
                        <td>' . $deptName . '</td>
                        <td>' . number_format(($total_availability) / $count, 2) . '%</td>
                        <td>' . number_format(($total_overall_performance) / $count, 2) . '%</td>
                        <td>' . number_format(($total_performance) / $count, 2) . '%</td>
                        <td>' . $avg_qcr . '%</td>
                        <td>' . number_format(($total_oee) / $count, 2) . '%</td>
                        <td>' . number_format(($idleTime / 60), 2) . '</td>
                        <td>' . number_format(($idleTime / 60) * $row->machine_hrcost, 2) . '</td>
                        <td>' . number_format($total_load_unload_time / 60, 2) . '</td>
                        ' . $td . '
                        <td>' . $total_production_qty . '</td>  
                        </tr>';
                        $totalAvailability += ($total_availability / $count);
                        $totalOverallPerformance += ($total_overall_performance / $count);
                        $totalPerformance += ($total_performance / $count);
                        $totalQualityRate += ($total_quality_rate / $count);
                        $totalOee += ($total_oee / $count);
                        $totalLossHours += $idleTime / 60;
                        $totalLossHoursINR += $idleTime / 60 * $row->machine_hrcost;
                        $totalLoadUnloadTime += $total_load_unload_time / 60;
                        $totalProductionQty += $total_production_qty;
                        $totalOkQty += $total_ok_qty;
                        $deptCount++;
                    }
                endif;
            }
            $td = $this->productionReportsNew->getTotaIdleTimeReasonForOee($data['fromDate'], $data['date']);
            $totalRecord = count($deptData);
            $avg_qc_rate = ($totalProductionQty > 0) ? round((($totalOkQty * 100) / $totalProductionQty),2) : 0;
            $oee_avg = round(((($totalAvailability / 100) * ($totalPerformance / 100) * ($avg_qc_rate / 100)) * 100)/3,2);
            $tfoot = '<tr class="text-center" style="font-weight : bold">
            <td class="text-right" colspan="2">Total</td>
            <td>' . (!empty($deptCount) ? number_format(($totalAvailability) / $deptCount, 2) : 0) . '%</td>
            <td>' . (!empty($deptCount) ? number_format(($totalOverallPerformance) / $deptCount, 2) : 0) . '%</td>
            <td>' . (!empty($deptCount) ? number_format(($totalPerformance) / $deptCount, 2) : 0) . '%</td>
            <td>' . $avg_qc_rate . '%</td>
            <td>' . (!empty($deptCount) ? number_format(($totalOee) / $deptCount, 2) : 0) . '%</td>
            <td>' . number_format($totalLossHours, 2) . '</td>
            <td>' . number_format($totalLossHoursINR, 2) . '</td>
            <td>' . number_format($totalLoadUnloadTime, 2) . '</td>
            ' . $td . '
            <td>'.$totalProductionQty.'</td>  
            </tr>';
        endif;
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    public function vendorTracking()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'VENDOR GOODS TRACKING';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->vendor_tracking, $this->data);
    }

    public function getVendorTracking()
    {
        $data = $this->input->post();
        $errorMessage = array();
		if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            
            $tbody=''; $i=1;$materialData  = array();
            $totalOut=0;$totalIn=0;$totalPend=0;

            if($data['report_type'] == 1):
                $goodsTrackingData = $this->productionReportsNew->getVendorGoodsTrackingData($data);
                foreach($goodsTrackingData as $row):
                    $tbody .= '<tr>
                        <td>'. $i++.'</td>
                        <td>'.formatDate($row->challan_date).'</td>
                        <td>'.getPrefixNumber($row->challan_prefix, $row->challan_no).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.$row->out_qty.'</td>
                        <td>'.$row->in_qty.'</td>
                        <td>'.($row->out_qty - $row->in_qty).'</td>
                    </tr>';
                    $totalOut+=$row->out_qty; $totalIn+=$row->in_qty; $totalPend+=($row->out_qty - $row->in_qty);
                endforeach;
            endif;

            if($data['report_type'] == 2):
                $trackingData = $this->productionReportsNew->getVendorTrackingData($data);
                if(!empty($trackingData)):
                    foreach($trackingData as $row): 
                        $materialData = json_decode($row->material_data);
                        $z=0;$item="";$out_qty='';$in_qty='';$pending_qty='';
                        
                        if(!empty($materialData)):
                            foreach($materialData as $mdata):
                                $itemData = $this->item->getItem($mdata->item_id);
                                if($z==0) {
                                    $item .= $itemData->item_name;
                                    $out_qty .= $mdata->out_qty;
                                    $in_qty .= $mdata->in_qty;
                                    $pending_qty .= ($mdata->out_qty - $mdata->in_qty);
                                }else{
                                        $item .= ',<br>'.$itemData->item_name;
                                        $out_qty .= ',<br>'.$mdata->out_qty;
                                        $in_qty .= ',<br>'.$mdata->in_qty;
                                        $pending_qty .= ',<br>'.($mdata->out_qty - $mdata->in_qty);
                                } $z++;
                            endforeach;
                        endif;
                        if($pending_qty > 0):
                            $tbody .= '<tr>
                                <td>'. $i++.'</td>
                                <td>'.formatDate($row->challan_date).'</td>
                                <td>'.getPrefixNumber($row->challan_prefix, $row->challan_no).'</td>
                                <td>'.$row->party_name.'</td>
                                <td>'.$item.'</td>
                                <td>'.$out_qty.'</td>
                                <td>'.$in_qty.'</td>
                                <td>'.$pending_qty.'</td>
                            </tr>';
                            $totalOut+=$out_qty; $totalIn+=$in_qty; $totalPend+=$pending_qty;
                        endif;
                    endforeach;
                endif;
            endif;            

            $tfoot = '<tr class="thead-info">
                <th colspan="5">TOTAL</th>
                <th>' . $totalOut . '</th>
                <th>' . $totalIn . '</th>
                <th>' . $totalPend . '</th>
            </tr>';

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    //Created By Karmi @10/08/2022
    public function fgPlaning()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'FG PLANNING';
        $this->data['itemDataList'] = $this->item->getItemList(1);        
        $this->load->view($this->fg_planing, $this->data);
    }

    public function getFGPlaning()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getFGPlaning($data);

        $i = 1;
        $tbody = "";
        $totalReqQty = 0; $stock =0;
        if (!empty($result)) :
            foreach ($result as $row) :
                $qtyTD = '';
                $totalReqQty = $row->qty * $data['fg_qty'];
                $stock = $this->store->getItemStock($row->ref_item_id);
                $qtyTD .= '<td>' . ((!empty($stock) && !empty($stock->qty)) ? abs($stock->qty) : '-') . '</td>';
                $tbody .= '<tr class="text-center">
                            <td>' . $i++ . '</td>
                            <td>' . $row->item_code . ' [' . $row->item_name . ']</td>
                            <td>' . $row->qty . ' (' . $row->unit_name . ')</td>
                            <td>' . floatVal($totalReqQty) . '  (' . $row->unit_name . ')</td>
                            ' . $qtyTD . '
                        </tr>';
            endforeach;
        endif;
        
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }
    
    /* Job Wise Costing */
    public function jobCosting($item_id = "")
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOB WISE PRODUCTION';
        $this->data['jobcardData'] = $this->productionReportsNew->getJobcardList();
        $this->data['itemId'] = $item_id;
        $this->load->view($this->job_costing, $this->data);
    }

    public function getJobCosting()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getJobCosting($data);
        $this->printJson($result);
    }
    
    //Crerated By Karmi @14/08/2022
    public function jobcardWiseCosting()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOBCARD WISE COSTING';
        $this->data['jobcardData'] = $this->productionReportsNew->getCompletedJobcardList();
        $this->load->view($this->jobcard_wise_costing, $this->data);
    }

    public function getJobCardWiseCosting()
    {
        $data = $this->input->post();
        $jobCardData = $this->jobcard_v3->getJobCard($data['job_id']);
        
        $process = explode(",",$jobCardData->process);
        $i=1; $tbody=""; $totalCosting=0; $stock=0;
        foreach($process as $process_id):
            $result = $this->productionReportsNew->getJobCardWiseCosting($data,$process_id,$jobCardData->product_id);
            if(!empty($result)) :
                foreach ($result as $row) :
                    $costing = (!empty($row->costing) && !empty($row->outQty))? $row->costing * $row->outQty : 0;
                    $tbody .= '<tr class="text-center">
                                <td>'.$i++.'</td>
                                <td>'.$row->process_name.'</td>
                                <td>'.$row->outQty.'</td>
                                <td>'.floatVal($row->costing).'</td>
                                <td>'.floatVal($costing).'</td>
                            </tr>';
                    $totalCosting += $costing;
                endforeach;
            endif;
        endforeach;
        $tfoot = '<tr class="thead-info">
					<th colspan="4">TOTAL</th>
					<th>' . $totalCosting . '</th>
				</tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }
    
    public function jobMaterialTracking(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Job Material Tracking';
        $this->load->view($this->job_material_tracking, $this->data);
    }

    public function getJobMaterialTrackingData(){
        $postData = $this->input->post();
        $materialData = $this->productionReportsNew->getJobWiseRequiredMaterial($postData);
        $tbodyData="";
        if(!empty($materialData)){
            $i=1;
            foreach($materialData as $row){
                
                $lastLog = $this->jobcard_v3->getLastTrans($row->job_card_id);
                $last_activity=((!empty($lastLog))? $lastLog->updated_at : "");
                
                // last activity
                $firstdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
                $seconddate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
                $thirdate = date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))));
                $lastAdate = date('Y-m-d', strtotime($last_activity)); 

                $color='';$usedQty = 0;
                if($lastAdate >= $firstdate) { $color="text-primary"; } 
                elseif($lastAdate == $seconddate) { $color="text-dark"; } 
                else { $color="text-danger"; }
                $approvalData = $this->productionReportsNew->getFirsJobOutQty($row->job_card_id);
                $last_activity = '<a href="javascript:void(0);" class="'.$color.' "><b>'.$last_activity.'</b></a>';
                if(!empty($approvalData)){$usedQty = ($approvalData->used_qty*$row->qty);}
                
                
                
                $scrapData = [];
                if(!empty($row->scrap_group)){
                    $scrapData = $this->productionReportsNew->getScrapQty(['ref_id'=>$row->job_card_id, 'item_id'=>$row->scrap_group, 'ref_type'=>18]);
                }
                $scrapQty = (!empty($scrapData->scrap_qty)?round($scrapData->scrap_qty,3):0);
                $row->scrap_qty = $row->scrap_qty + $scrapQty;
                
                $stock_qty = $row->issue_qty - $usedQty - abs($row->return_qty) - abs($row->scrap_qty);
                
                $tbodyData.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
                    <td>'.formatDate($row->job_date).'</td>
                    <td>'.$row->product_code.'</td>
                    <td>'.floatVal($row->job_qty).'</td>
                    <td>'.floatVal($approvalData->pend_qty).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.round(($row->qty*$row->job_qty),2).'</td>
                    <td>'.round($row->issue_qty,2).'</td>
                    <td>'.round($usedQty,2).'</td>
                    <td>'.round(abs($row->scrap_qty),2).'</td>
                    <td>'.round(abs($row->return_qty),2).'</td>
                    <td>'.formatDate($row->return_date).'</td>
                    <td>'.abs(round($stock_qty,2)).'</td>
                    <td>'.$last_activity.'</td>
                </tr>';
            }
        }
        $this->printJson(['status' => 1, 'tbody' => $tbodyData]);
    }
    
    /* Customer's Order Monitoring */
	public function orderMonitor()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'CUSTOMER ORDER MONITORING REPORT';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->order_monitor,$this->data);
    }

    public function getOrderMonitor()
    {
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $orderData = $this->salesReportModel->getOrderMonitor($data);
            $tbody="";$i=1;//$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($orderData as $row):
                $data['id'] = $row->id;
                $data['sales_type'] = $row->sales_type;
                $data['trans_date'] = $row->trans_date;
                $invoiceData = $this->salesReportModel->getInvoiceData($data);
                $invoiceCount = count($invoiceData);
                $rp = 0;
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->doc_date).'</td>
                    <td>'.$row->doc_no.'</td>
                    <td>'.$row->party_code.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.((!empty($row->prod_target_date))?  formatDate($row->prod_target_date) : formatDate($row->cod_date)).'</td>
                    <td>'.$row->drg_rev_no.'</td>
                    <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->emp_name.'</td>';
                    if($invoiceCount > 0):
                        $j=1;$dqty=0; $totalQty=0; $dPr=0;
                        foreach($invoiceData as $invRow):
                            $dqty = (!empty($invRow->dqty))?$invRow->dqty:0;
                            $totalQty += $dqty;
                            $qtyD = ($row->qty) - $totalQty;
                            $dPr = (( $qtyD * 100 ) / $row->qty);
                            $schInvNo = (!empty($invRow->inv_no))?getPrefixNumber($invRow->inv_prefix,$invRow->inv_no):getPrefixNumber($invRow->trans_prefix,$invRow->trans_no);
                            $tbody.='<td>'.$schInvNo.'</td>
                                    <td>'.formatDate($invRow->trans_date).'</td>
                                    <td>'.floatval($dqty).'</td>
                                    <td>'.floatval($totalQty).'</td>
                                    <td>'.$qtyD.'</td>
                                    <td>'.number_format($dPr,2).'%</td>';
                            if($j != $invoiceCount){
								$tbody.='</tr><tr>
										<td>'.$i++.'</td>
										<td>'.formatDate($row->doc_date).'</td>
										<td>'.$row->doc_no.'</td>
										<td>'.$row->party_code.'</td>
										<td>'.$row->item_code.'</td>
										<td>'.floatVal($row->qty).'</td>
										<td>'.((!empty($row->prod_target_date))?  formatDate($row->prod_target_date) : formatDate($row->cod_date)).'</td>
										<td>'.$row->drg_rev_no.'</td>
										<td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
										<td>'.formatDate($row->trans_date).'</td>
										<td>'.$row->emp_name.'</td>'; 
							}
                            $j++;
                        endforeach;
                    else:
                        $tbody.='<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>';
                    endif;
                $tbody .= '</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    public function materialRequirementsPlanning()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Material Requirements Planning';
        $this->data['itemList'] = $this->salesReportModel->getOrderItemList();
        $this->load->view($this->material_requirements_planning,$this->data);
    }

    public function getMaterialRequirementsPlanning()
    {
        $data = $this->input->post();

        $stockData = $this->store->getItemStockBatchWise(['item_id' => $data['item_id'], 'stock_required' => 1]);
        $stockTbody = ""; $stockTfoot = ""; $totalStockQty = 0;
        if(!empty($stockData)):
            $i=1;
            foreach($stockData as $row):
                $stockTbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>['.$row->store_name.'] '.$row->location.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->qty.'</td>
                </tr>';
                $totalStockQty += $row->qty; 
            endforeach;            
        else:
            $stockTbody .= '<tr>
                <td colspan="4" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;

        $stockTfoot = '<tr>
            <th colspan="3" class="text-right">Total</th>
            <th>'.$totalStockQty.'</th>
        </tr>';
        $data['pending_qty'] = $data['pending_qty'] - $totalStockQty;

        $wipTbody='';$wipTfoot='';$totalWIPQty =0;
        $wipData = $this->productionReportsNew->getWIPStockData($data['item_id']);
        if(!empty($wipData)):
            $i=1;
            foreach($wipData as $row):
                $wipTbody.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.(!empty($row->process_name)?$row->process_name:'Raw Material').'</td>
                    <td>'.floatVal($row->wip_qty).'</td>
                </tr>';
                $totalWIPQty += $row->wip_qty;
            endforeach;
        else:
            $wipTbody .= '<tr>
                <td colspan="3" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;

        $wipTfoot = '<tr>
            <th colspan="2" class="text-right">Total</th>
            <th>'.$totalWIPQty.'</th>
        </tr>';
        $data['pending_qty'] = $data['pending_qty'] - $totalWIPQty;

        $materialTbody = "";$req_qty = 0;
        if($data['pending_qty'] > 0):
            $req_qty = $data['pending_qty'];
            $itemBom = $this->item->getProductKitData($data['item_id']);
            if(!empty($itemBom)):
                $i=1;
                foreach($itemBom as $row):
                    $itemStock = $this->store->getItemStockGeneral(['item_id' => $row->ref_item_id]);
                    $materialTbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.($data['pending_qty'] * $row->qty).'</td>
                        <td>'.((!empty($itemStock->qty) && $itemStock->qty > 0)?$itemStock->qty:0).'</td>
                    </tr>';
                endforeach;
            else:
                $materialTbody .= '<tr>
                    <td colspan="4" class="text-center">
                        Material not found.
                    </td>
                </tr>';
            endif;
        else:
            $materialTbody .= '<tr>
                <td colspan="4" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;
        
        
        $this->printJson(['status' => 1,'stockTbody'=>$stockTbody, 'stockTfoot'=>$stockTfoot, 'wipTbody'=>$wipTbody, 'wipTfoot'=>$wipTfoot, 'materialTbody'=>$materialTbody,'req_qty'=>$req_qty,'totalStockQty'=>$totalStockQty,'totalWIPQty'=>$totalWIPQty]);
    }

    /**
    * Machine Log Summary Report 
    * Created By : Chauhan Milan
    * Created At : 04-04-2023
    * Note : SYNC Log Data from Smarttrack.nativebittechnologies.com and import in device_log table
    **/
    function machineLogSummary()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Machine Log Summary';
        $this->data['machineList'] = $this->machine->getMachineList(['is_device_no'=>1]);
        $this->load->view($this->machine_log_summary,$this->data);
    }

    public function getMachineLogSummaryData()
    {
        $data = $this->input->post();

        $result = $this->productionReportsNew->syncDeviceData($data);

        if($result['status'] == 1):
            $this->productionReportsNew->changeSyncStatus($result['ids']);

            $result = $this->productionReportsNew->getMachineLogSummaryData($data);

            $tbody = '';$i=1;$totalPlanQty = 0; $totalProdQty = 0;$totalProdLossQty = 0;
            foreach($result as $row):
                $row->rej_qty = "";
                $row->rw_qty = "";
                $row->qaulity_ration = "";
                $row->l_u_time = 0;
                $row->total_ct = ($row->cycle_time + $row->l_u_time);

                $row->plan_qty = (!empty($row->total_ct))?round(($row->sec_diff/$row->total_ct),0):0;
                $row->prod_loss_qty = $row->plan_qty - $row->prod_qty;

                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$row->duration.'</td>
                    <td>'.$row->job_prefix.$row->job_no.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.$row->machine_code.'</td>
                    <td>'.$row->process_name.'</td>
                    <td>'.$row->operator_name.'</td>
                    <td>'.$row->cycle_time.'</td>
                    <td>'.$row->l_u_time.'</td>
                    <td>'.$row->total_ct.'</td>
                    <td>'.$row->plan_qty.'</td>
                    <td>'.$row->prod_qty.'</td>
                    <td>'.$row->rej_qty.'</td>
                    <td>'.$row->rw_qty.'</td>
                    <td>'.$row->qaulity_ration.'</td>
                    <td>'.$row->prod_loss_qty.'</td>
                </tr>';
                $i++;

                $totalPlanQty += $row->plan_qty; 
                $totalProdQty += $row->prod_qty;
                $totalProdLossQty += $row->prod_loss_qty;
            endforeach;

            $tfoot = '<tr>
                <th class="text-right" colspan="10">Total</th>
                <th class="text-center">'.$totalPlanQty.'</th>
                <th class="text-center">'.$totalProdQty.'</th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center">'.$totalProdLossQty.'</th>
            </tr>';

            $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
        else:
            $this->printJson($result);
        endif;
    }
    /* End Machine Log Summary */
    
    public function costingReport(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'PRODUCTION REPORT';
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->load->view($this->costing_report,$this->data);
    }
    
    /*Created By @Raj 08-07-2024*/
    public function getcostingReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $jobData = $this->productionReportsNew->getcostingReportData($data);
            $tbody="";$i=1;
            if(!empty($jobData)):
                foreach($jobData as $row):                    
                    $qty = (!empty($row->total_ok_qty)) ? $row->total_ok_qty : 0;
					$rej_qty = (!empty($row->total_rejection_qty)) ? $row->total_rejection_qty : 0;
                    $prod_qty = $qty + $rej_qty;
                    $process_cost = (!empty($row->process_costing)) ? round($row->process_costing * $prod_qty,2) : 0;

                    $totalCost = (!empty($row->rm_cost)) ? round($row->rm_cost * $prod_qty,2) : 0;
					$totalCost += $process_cost;

                    $cpp = (!empty($prod_qty)) ? round($totalCost / $prod_qty,2) : 0;
					$acpp = (!empty($prod_qty)) ? round($totalCost / $qty,2) : 0;

                    $tbody .= '<tr>
								<td class="text-center">'.$i++.'</td>
								<td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
								<td>'.$row->total_ok_qty.'</td>
								<td>'.$row->total_rejection_qty.'</td>
								<td>'.$row->rm_cost.'</td>
								<td>'.$row->process_costing.'</td>
								<td>'.number_format($totalCost,3).'</td>
								<td>'.$cpp.'</td>
								<td>'.$acpp.'</td>';
                    $tbody.='</tr>';
                endforeach;
				
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    /**
    * JOB COSTING REPORT 
    * Created By : Chauhan Milan
    * Created At : 24-07-2024
    * Note : Select job no to get process wise job coting 
    **/
    public function jobCostingReport(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOB COSTING REPORT';
        $this->data['jobCardList'] = $this->productionReportsNew->getJobcardList(['job_status'=>[2,3,4,5,6]]);
        $this->load->view($this->job_costing_report,$this->data);
    }

    public function getJobCostingData(){
        $data = $this->input->post();
        $jobCardData = $this->jobcard_v3->getJobcard($data['job_card_id']);
        $reqMaterials = $this->jobcard_v3->getProcessWiseRequiredMaterial($jobCardData);
        $reqMaterials = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData'][0]:'';
        $supplierDetail = $this->jobcard_v3->getSupplierDetailOnBatchNo(['batch_no'=>$reqMaterials['heat_no']]);
        $fgDetail = $this->item->getItem($jobCardData->product_id);
        $rmDetail = $this->item->getItem($reqMaterials['item_id']);

        $supplier_name = (!empty($supplierDetail)) ? '<br><small>('.$supplierDetail->party_name.')</small>' : '';
        
        if($fgDetail->currency!='INR')
        {         
            $inr = $this->salesReportModel->getCurrencyConversion($fgDetail->currency);
            if(!empty($inr)){ $fgDetail->price = $inr[0]->inrrate * $fgDetail->price;}
        }
        
        $fgPrice = ((!empty($fgDetail->price))?round($fgDetail->price,2):"");
        $rmPrice = ((!empty($supplierDetail->price))?$supplierDetail->price:$rmDetail->price);
        $rmWpcs = ((!empty($reqMaterials['bom_qty']))?$reqMaterials['bom_qty']:"");

        $html = '<div class="col-lg-12 col-xlg-12 col-md-12">
            <div class="card">
                <div class="titleText">JOB DETAIL</div>
                <div class="card-body scrollable" style="height:auto;border-bottom: 5px solid #45729f">
                    <table class="table">
                        <tr>
                            <th>Job Card No.</th>
                            <td>: '.getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no).'</td>
                            <th>Product </th>
                            <td>: '.$jobCardData->product_code.'</td>
                            <th>Order Quatity </th>
                            <td>: '.$jobCardData->qty.' <small>'.$jobCardData->unit_name.'</small></td>
                        </tr>
                        <tr>
                            <th>Material Name</th>
                            <td>: '.((!empty($reqMaterials['item_name']))?$reqMaterials['item_name'].$supplier_name:'').'</td>
                            <th>Heat No. </th>
                            <td>: '.((!empty($reqMaterials['heat_no']))?$reqMaterials['heat_no']:"").'</td>
                            <th>Issue Qty. </th>
                            <td>: '.((!empty($reqMaterials['issue_qty']))?$reqMaterials['issue_qty']:"").'</td>
                        </tr>
                        <tr>
                            <th>RM Rate as per GRN</th>
                            <td>: '.$rmPrice.'</td>
                            <th>RM KG as per BOM</th>
                            <td>: '.$rmWpcs.'</td>
                            <th>FG Price per Pcs</th>
                            <td>: '.$fgPrice.'</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>';

        $html .= '<div class="col-lg-12 col-xlg-12 col-md-12">
            <div class="card">
                <div class="titleText">COSTING DETAIL</div>
                <div class="card-body scrollable" style="height:auto;border-bottom: 5px solid #45729f;">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered ">
                            <thead class="thead-info">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th class="text-left">Process Name</th>
                                    <th class="text-left">Vendor</th>
                                    <th>Inward <br> Qty</th>
                                    <th>Inward <br> Cost</th>
                                    <th>Cost <br> Per Pcs</th>
                                    <th>Dept <br> MHR</th>
                                    <th>Cycle Time<br>(in minutes)</th>
                                    <th>Dept Cost<br>addition<br>Per Pcs</th>
                                    <th>Cumulative Cost<br>Per Pcs</th>
                                    <th>Qty. Pend. For <br> Move</th>
                                    <th>Cost of<br>Pend. for Move</th>
                                    <th>Qty. Moved to <br>Next</th>
                                    <th>Cost of <br> Moved to <br>Next</th>
                                    <th>Reject <br> Found</th>
                                    <th>Cost of<br>Rejection</th>
                                </tr>
                            </thead>
                            <tbody>';

            $i=1;$process = explode(",","0,".$jobCardData->process);
            $cumCostPerPcs = $costOfPendMove = $moveToNextCost = $rejCost = $produstionCost = 0;
            $lostItemTotal = $rejCostTotal = 0;
            foreach($process as $process_id):
                $row = new stdClass;
                $jobApprovalData = $this->processMovement->getApprovalData(['job_card_id'=>$jobCardData->id,'in_process_id'=>$process_id]);

                $row->process_id = $process_id;
                $row->process_name = (!empty($jobApprovalData))?$jobApprovalData->in_process_name:((!empty($process_id))?$this->process->getProcess($process_id)->process_name:"Raw Material");
                $row->vendor = (!empty($jobApprovalData))?$jobApprovalData->vendor:"";
                $row->inward_qty = (!empty($jobApprovalData->inward_qty))?$jobApprovalData->inward_qty:0;
                $row->in_qty = (!empty($jobApprovalData))?$jobApprovalData->in_qty:0;
                $row->out_qty = (!empty($jobApprovalData))?$jobApprovalData->out_qty:0;
                $row->total_ok_qty = (!empty($jobApprovalData))?$jobApprovalData->total_ok_qty:0;
                $row->total_rejection_qty = (!empty($jobApprovalData->total_rejection_qty))?$jobApprovalData->total_rejection_qty:0;
                $row->total_rej_belongs = (!empty($rej_belongs))?$rej_belongs:0;
                $row->total_rework_qty =(!empty($logData->rework_qty))?$logData->rework_qty:0;

                $completeQty = $row->total_ok_qty + $row->total_rejection_qty;
                $row->pending_to_move = round(($row->in_qty - $completeQty),2);

                $deptCostPerPcs = $inwardCost = $costPerPcs = 0;                
                $productCycleTime = array();
                if(empty($process_id)):
                    $inwardCost = round(($row->inward_qty * $rmPrice * $rmWpcs),2);
                    $cumCostPerPcs = $deptCostPerPcs = $costPerPcs = round(($rmPrice * $rmWpcs),2);
                else:
                    $productCycleTime = $this->item->getProductCycleTime(['item_id'=>$jobCardData->product_id,'process_id'=>$process_id]);
                    $deptCostPerPcs = (!empty($productCycleTime->cycle_time_minutes) && !empty($productCycleTime->mhr))?round(($productCycleTime->cycle_time_minutes * $productCycleTime->mhr / 60),2):0;
                    //if($row->vendor != "In House"):
                        //$costPerPcs = $deptCostPerPcs = (!empty($productCycleTime))?$productCycleTime->mhr:0;
                        //$productCycleTime->mhr = 0;
                        //$productCycleTime->cycle_time_minutes = 0;
                    //endif;
                    $cumCostPerPcs = $cumCostPerPcs + $deptCostPerPcs;
                endif;
                
                if($process[(COUNT($process) - 1)] == $process_id){
                    $cumCostPerPcs = $fgPrice;
                }else{
                    $produstionCost = round(($row->out_qty * $cumCostPerPcs),2);
                }

                $costOfPendMove = round(($row->pending_to_move * $cumCostPerPcs),2);
                $moveToNextCost = round(($row->out_qty * $cumCostPerPcs),2);
                $rejCost = round(($row->total_rejection_qty * $cumCostPerPcs),2);

                $html .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$row->process_name.'</td>
                    <td>'.$row->vendor.'</td>
                    <td>'.round($row->inward_qty,2).'</td>
                    <td>'.$inwardCost.'</td>
                    <td>'.$costPerPcs.'</td>
                    <td>'.((!empty($productCycleTime))?$productCycleTime->mhr:"").'</td>
                    <td>'.((!empty($productCycleTime))?round($productCycleTime->cycle_time_minutes,2):"").'</td>
                    <td>'.$deptCostPerPcs.'</td>
                    <td>'.$cumCostPerPcs.'</td>
                    <td>'.$row->pending_to_move.'</td>
                    <td>'.$costOfPendMove.'</td>
                    <td>'.round($row->out_qty,2).'</td>
                    <td>'.$moveToNextCost.'</td>
                    <td>'.round($row->total_rejection_qty,2).'</td>
                    <td>'.$rejCost.'</td>
                </tr>';
                $i++;

                $lostItemTotal += $costOfPendMove;
                $rejCostTotal += $rejCost;
            endforeach;

            $html .= '<tr>
                <td>'.$i.'</td>
                <td>Packing Area</td>
                <td>In House</td>
                <td>'.round($row->out_qty,2).'</td>
                <td></td>
                <td>'.$fgPrice.'</td>
                <td></td>
                <td></td>
                <td>'.$fgPrice.'</td>
                <td>'.$fgPrice.'</td>
                <td></td>
                <td></td>
                <td>'.round($row->out_qty,2).'</td>
                <td>'.round(($row->out_qty * $fgPrice),2).'</td>
                <td></td>
                <td></td>
            </tr>';

        $html .= '          </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>';

        $totalSellingPrice = round(($row->out_qty * $fgPrice),2);
        $totalProductionCost = $produstionCost;
        $totalItemLostCost = $lostItemTotal;
        $totalRejectionCost = $rejCostTotal;
        $profitForJobCard = ($totalSellingPrice - $totalProductionCost -  $totalItemLostCost - $totalRejectionCost);

        $html .= '<div class="col-lg-12 col-xlg-12 col-md-12">
            <div class="card">
                <div class="titleText">FINAL COST DETAIL</div>
                <div class="card-body scrollable" style="height:30vh;border-bottom: 5px solid #45729f">
                    <table class="table">
                        <tr>
                            <th>Total Sellig Price</th>
                            <th>: '.$totalSellingPrice.'</th>
                        </tr>
                        <tr>
                            <th>Total Production Cost</th>
                            <th>: '.$totalProductionCost.'</th>
                        </tr>
                        <tr>
                            <th>Total Item lost Cost</th>
                            <th>: '.$totalItemLostCost.'</th>
                        </tr>
                        <tr>
                            <th>Total Rejection Cost</th>
                            <th>: '.$totalRejectionCost.'</th>
                        </tr>
                        <tr>
                            <th>Profit for Job Card</th>
                            <th>: '.$profitForJobCard.'</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>';

        $this->printJson(['status'=>1,'html'=>$html]);
    }
    
    public function jobCostingComparison(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'JOB COSTING COMPARISON';
        $this->data['jobCardList'] = $this->productionReportsNew->getJobcardList(['job_status'=>[4]]);
        $this->load->view($this->job_costing_comparison,$this->data);
    }

    public function getCostList()
    {
        $data = $this->input->post();
        $costingData = $this->costingModel->getCostingData(['item_id'=>$data['item_id'],'active_revision'=>1]);
        $option = '<option value="">Select Cost</option>';
        foreach ($costingData as $row) :
            $option .= '<option value="' . $row->id . '" >' . formatDate($row->cost_date).' [Rev No : '.$row->rev_no.']' . '</option>';
        endforeach;

        $this->printJson(['status' => 1, 'option' => $option]);
    }

    public function getJobCostingComparisonData(){
        $data = $this->input->post();
        $jobCardData = $this->jobcard_v3->getJobcard($data['job_card_id']);
        $reqMaterials = $this->jobcard_v3->getProcessWiseRequiredMaterial($jobCardData);
        print_r($reqMaterials['resultData']);exit;
        $reqMaterials = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData'][0]:'';
        $supplierDetail = $this->jobcard_v3->getSupplierDetailOnBatchNo(['batch_no'=>$reqMaterials['heat_no']]);
        $fgDetail = $this->item->getItem($jobCardData->product_id);
        $rmDetail = $this->item->getItem($reqMaterials['item_id']);

        $supplier_name = (!empty($supplierDetail)) ? '<br><small>('.$supplierDetail->party_name.')</small>' : '';
        
        if($fgDetail->currency!='INR')
        {         
            $inr = $this->salesReportModel->getCurrencyConversion($fgDetail->currency);
            if(!empty($inr)){ $fgDetail->price = $inr[0]->inrrate * $fgDetail->price;}
        }
        
        $fgPrice = ((!empty($fgDetail->price))?round($fgDetail->price,2):"");
        $rmPrice = ((!empty($supplierDetail->price))?$supplierDetail->price:$rmDetail->price);
        $rmWpcs = ((!empty($reqMaterials['bom_qty']))?$reqMaterials['bom_qty']:"");

        $html = '<div class="col-lg-12 col-xlg-12 col-md-12">
            <div class="card">
                <div class="titleText">JOB DETAIL</div>
                <div class="card-body scrollable" style="height:auto;border-bottom: 5px solid #45729f">
                    <table class="table">
                        <tr>
                            <th>Job Card No.</th>
                            <td>: '.getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no).'</td>
                            <th>Product </th>
                            <td>: '.$jobCardData->product_code.'</td>
                            <th>Order Quatity </th>
                            <td>: '.$jobCardData->qty.' <small>'.$jobCardData->unit_name.'</small></td>
                        </tr>
                        <tr>
                            <th>Material Name</th>
                            <td>: '.((!empty($reqMaterials['item_name']))?$reqMaterials['item_name'].$supplier_name:'').'</td>
                            <th>Heat No. </th>
                            <td>: '.((!empty($reqMaterials['heat_no']))?$reqMaterials['heat_no']:"").'</td>
                            <th>Issue Qty. </th>
                            <td>: '.((!empty($reqMaterials['issue_qty']))?$reqMaterials['issue_qty']:"").'</td>
                        </tr>
                        <tr>
                            <th>RM Rate as per GRN</th>
                            <td>: '.$rmPrice.'</td>
                            <th>RM KG as per BOM</th>
                            <td>: '.$rmWpcs.'</td>
                            <th>FG Price per Pcs</th>
                            <td>: '.$fgPrice.'</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>';
        //JOb Cost HTML
        $job_cost_detail = '';
        $jobLogData = $this->productionReportNew->getActualMfgCost(['job_card_id'=>$data['job_card_id']]);
        if(!empty($jobLogData)){
            //RM Costing
            $job_cost_detail .= '<table class="table jpExcelTable"> 
                                    <thead class="bg-light">
                                        <tr>
                                            <th colspan="2" class="text-center bg-light">Raw Material Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="width:40%;" >Material Specification</th>
                                            <td style="width:40%;" >Material Specification</td>
                                        </tr>
                                    </tbody>
                                </table>';
        }
        $this->printJson(['status'=>1,'master_cost_detail'=>$master_cost_detail,'job_cost_detail'=>$job_cost_detail]);
    }
}
?>