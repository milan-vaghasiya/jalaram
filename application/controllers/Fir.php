<?php
class fir extends MY_Controller
{
    private $indexPage = "fir/index";
    private $formPage = "fir/accept_form";
    private $firDimension = "fir/fir_dimension";
    private $pending_fir_index = "fir/pending_fir_index";
    private $fir_index = "fir/fir_index";
    private $confirmView = "fir/fir_view";
    private $lot_form = "fir/form";
    private $sequence_form = "fir/sequence_form";
    private $testReport = "fir/tc_report";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "FIR Report";
        $this->data['headData']->controller = "fir";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader("firInward");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->fir->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFIRInwardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function pendingFirIndex()
    {
        $this->data['tableHeader'] = getQualityDtHeader("pendingFir");
        $this->load->view($this->pending_fir_index, $this->data);
    }

    public function getPendingFirDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->fir->getPendingFirDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPendingFirData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptFIR(){ 
        $id= $this->input->post('id');
        $this->data['jobData'] =$logData= $this->productionLog->getLogs($id);
        $approveData = $this->processMovement->getApprovalData(['in_process_id'=>$logData->out_process_id,'job_card_id'=>$logData->job_card_id]);
        $this->data['jobData']->next_approval_id = $approveData->id;
        $this->load->view($this->formPage,$this->data);
    }

    public function addFirLot(){
        $id = $this->input->post('id');
        $this->data['firData'] =$firData = $logData= $this->productionLog->getLogs($id);
        $approveData = $this->processMovement->getApprovalData(['in_process_id'=>$firData->out_process_id,'job_card_id'=>$firData->job_card_id]);
        $this->data['firData']->next_approval_id = $approveData->id;
        $this->data['movementList'] = $this->fir->getFIRPendingJobTrans(['job_card_id'=>$firData->job_card_id]);
        $lot_no = $this->fir->getMaxLotNoJobcardWise(['job_card_id'=>$firData->job_card_id]);
        $fir_number="FIR/".getPrefixNumber($firData->job_prefix,$firData->job_no).'/'.$lot_no;
        $this->data['fir_prefix'] = "FIR/";
        $this->data['fir_no'] =$lot_no;
        $this->data['fir_number'] =$fir_number;
        $this->data['fg_no'] =$fg_no = $this->fir->getMaxFGNo(['item_id'=>$firData->product_id]);
        $year = n2y(date('Y'));
        $month = n2m(date('m'));
        $this->data['fg_batch_no'] =$year.$month.sprintf('%02d',$fg_no);

        // $prsData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$firData->product_id,'process_id'=>$firData->in_process_id]);

        // $this->data['firDimensionData'] = $this->controlPlan->getCPDimenstion(['pfc_id'=>$prsData->pfc_process,'item_id'=>$firData->product_id,'control_method'=>'FIR']);
    
        // $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->lot_form,$this->data);
    }

    public function saveInward(){
        $data = $this->input->post();
        if($data['job_card_id'] == "")
            $errorMessage['job_card_id'] = "Jobcard is required.";
        if(empty($data['qty']) || $data['qty'] == "0.000"):
            $errorMessage['qty'] = "Quantity is required.";
        else:
            $fiStock = $this->productionLog->getLogs($data['job_trans_id']);
            // print_r(($fiStock->inward_qty - $fiStock->in_qty));
            if($data['qty'] > ($fiStock->ok_qty - $fiStock->accepted_qty)){
                $errorMessage['qty'] = "Quantity is not available.";
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->fir->saveInward($data));
        endif;
    }   

    /**** Pending FIR Report Save */
    public function save(){
        $data = $this->input->post(); //print_r($data);exit;
        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Jobcard is required.";
        if(empty($data['total_ok_qty']) && empty($data['total_rej_qty']) && empty($data['total_rw_qty'])):
            $errorMessage['general_error'] = "OK Qty Or Rejection Qty. is required.";
        else:
            $totalQty = $data['total_ok_qty']+(!empty($data['total_rej_qty'])?$data['total_rej_qty']:0)+(!empty($data['total_rw_qty'])?$data['total_rw_qty']:0);
            if($totalQty != $data['qty']):
                $errorMessage['ok_qty'] = "Qty is not valid";
            endif;
       
          
                $i=1;
                
                $insqQtySum = array_sum($data['ok_qty'])+array_sum($data['ud_ok_qty'])+array_sum($data['rej_qty'])+array_sum($data['rw_qty']);
                if(empty($insqQtySum)){
                    $errorMessage['general_error'] = "Fill at least one parameter.";
                }else{
                    $totalRej = array_sum($data['rej_qty']);
                    if($data['total_rej_qty'] != $totalRej)
                        $errorMessage['general_error'] = "Rejection Qty does not match with Total Rejection Qty.";
                    
                    $totalRw = array_sum($data['rw_qty']);
                    if($data['total_rw_qty'] != $totalRw)
                        $errorMessage['general_error'] = "Rework Qty does not match with Total Rework Qty.";
                    foreach($data['dimension_id'] as $key=>$value){
                        $qty = $data['ok_qty'][$key]+$data['ud_ok_qty'][$key]+$data['rej_qty'][$key]+$data['rw_qty'][$key];
                    
                        if($qty > $data['qty']){
                            $errorMessage['insp_qty_'.$i] = "Quantity is invalid.";
                        }
                        $i++;
                    }
           
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->save($data);
            $this->printJson($result);
        endif;
    }

    public function firIndex($status = 0)
    {
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getQualityDtHeader("fir");
        $this->load->view($this->fir_index, $this->data);
    }

    public function getFirDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->fir->getFirDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFirData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function edit($id){
        $this->data['dataRow'] =$firData = $this->fir->getFIRMasterDetail($id);
        $this->data['firData'] =  $this->processMovement->getApprovalData($this->data['dataRow']->job_approval_id);
        $this->data['firDimensionData'] = $this->fir->getFIRDimensionData(['fir_id'=>$id]);
        $this->data['empData'] = $this->employee->getActiveEmpList();
        $this->load->view($this->firDimension,$this->data);
    }

    public function completeFirView(){
        $id = $this->input->post('id');
        $this->data['dataRow']=$dataRow = $this->fir->getFIRMasterDetail($id);
        $this->data['firData']= $approveData =  $this->processMovement->getApprovalData(['id'=>$dataRow->job_approval_id]);
        $this->data['firDimensionData'] =$paramData = $this->fir->getFIRDimensionData(['fir_id'=>$id]);
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();

        //Process
        $jobData = $this->jobcard_v3->getJobcard($dataRow->job_card_id);
        $processList = explode(',', $jobData->process);
        $dataRow->processOpt = '';
      

        //Machine
        $machineData = $this->item->getProcessWiseMachine($approveData->in_process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach ($machineData as $row) :
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
            $dataRow->machineOpt .= '<option value="' . $row->id . '" ' . $selectMac . '>[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
        endforeach;

        //Rej & Rew Form
        $in_process_key = array_keys($processList, $approveData->in_process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($processList as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $dataRow->stage = $html;
        $this->data['dataRow']->stage = $html;
        $this->load->view($this->confirmView,$this->data);
    }

    public function completeFir(){
        $data = $this->input->post();
        $errorMessage = array();
        $data['rej_qty'] = 0;
        if(!empty($data['rejection_reason'])):
            $data['rej_qty']= array_sum(array_column($data['rejection_reason'],'rej_qty'));
            $data['rej_reason'] = json_encode($data['rejection_reason']);
        endif;

        $data['rw_qty']= 0;
        if(!empty($data['rework_reason'])):
            $data['rw_qty']= array_sum(array_column($data['rework_reason'],'rw_qty'));
            $data['rw_reason'] = json_encode($data['rework_reason']);
           
        endif;
        // if(floatval($data['total_rw_qty']) != floatval($data['rw_qty'])){
        //     $errorMessage['rw_error'] = "Rework Qty is not valid";
        //     $this->printJson(['status' => 0, 'message' => $errorMessage]);
        // }
        if(floatval($data['total_rej_qty']) != floatval($data['rej_qty'])){
            $errorMessage['general_error'] = "Rejection Qty is not valid";
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        }
        $result = $this->fir->completeFir($data);

        $this->printJson($result);
    }

     
    public function delete()
    {
          $id = $this->input->post('id');
          if (empty($id)) :
              $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
          else :
              $result = $this->fir->delete($id);
              $this->printJson($result);
          endif;
    }

    /**** Pending FIR Report Save */
    public function saveLot(){
        $data = $this->input->post(); //print_r($data);exit;
        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Jobcard is required.";
       
        if (!isset($data['job_trans_id']))
            $errorMessage['orderError'] = "Please Check atleast one Transaction.";

        if (!empty($data['job_trans_id'][0])) :
            foreach ($data['job_trans_id'] as $key => $value) :
                if (empty($data['lot_qty'][$key])) :
                    $errorMessage['lotQty' . $value] = "Qty. is required.";
                else:
                    $jobTransData = $this->productionLog->getLogs($value);
                   
                    $pendingQty = $jobTransData->accepted_qty - $jobTransData->fir_qty;
                    if($data['lot_qty'][$key] > $pendingQty){
                        $errorMessage['lotQty' . $value] = "Qty. is invalid.";
                    }
                endif;
            endforeach;
        endif;
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->saveLot($data);
            $this->printJson($result);
        endif;
    }

    public function fir_pdf($id){
        $this->data['dataRow'] = $this->fir->getFIRMasterDetail($id);
        $this->data['firData'] =  $this->processMovement->getApprovalData(['id'=>$this->data['dataRow']->job_approval_id]);
        $this->data['firDimensionData'] =$paramData = $this->fir->getFIRDimensionData(['fir_id'=>$id]);
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('fir/fir_print',$this->data,true);
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:70px;"></td>
								<td class="org_title text-center" style="font-size:1.5rem;width:50%">Final Inspection Report</td>
								<td style="width:25%;" class="text-right"></td>
							</tr>
						</table><hr>';
		$htmlFooter = '
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='fir'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,30,20,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }

    public function saveDimension(){
        $data = $this->input->post();
        if(empty($data['in_qty']) || $data['in_qty']==0.000):
            $errorMessage['inQty'.$data['id']] = "In Qty is required.";
        else:
            $data['inspected_qty'] = $data['ok_qty']+(!empty($data['ud_ok_qty'])?$data['ud_ok_qty']:0)+(!empty($data['rej_qty'])?$data['rej_qty']:0)+(!empty($data['rw_qty'])?$data['rw_qty']:0);
            $dimData = $this->fir->getFIRDimensionDetail(['id'=>$data['id']]);
            if($dimData->lot_type == 2){
                if($data['inspected_qty'] < $dimData->sample_qty || $data['inspected_qty'] > $dimData->qty):
                    $errorMessage['insp_qty_'.$data['id']] = "Qty is not valid.";
                endif;   
                
            }else{
                if($data['inspected_qty'] > $data['in_qty']):
                    $errorMessage['insp_qty_'.$data['id']] = "Qty is not valid";
                endif;   
               
            }
            if(empty($data['inspector_id'])){
                $errorMessage['inspector_id_'.$data['id']] = "Inspector is not valid";
            }
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->saveDimension($data);
            $this->printJson($result);
        endif;
    }

    public function changeDimensionSequence(){
        $data = $this->input->post();
        $this->data['paramData'] = $this->fir->getFIRDimensionData(['fir_id'=>$data['id']]);
        $this->data['fir_id'] = $data['id'];
        $this->load->view($this->sequence_form,$this->data);
    }

    public function updateDimensionSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Dimension is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->fir->updateDimensionSequance($data));			
		endif;
    }

    public function clearDimension(){
        $data = $this->input->post(); 
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->fir->clearDimension($data);
            $this->printJson($result);
        endif;
    }

    public function saveSampleQty(){
        $data = $this->input->post(); 
        if(empty($data['sample_qty'])){
            $errorMessage['sample_qty'] = "Inspection qty required.";
        }else{
            $firData = $this->fir->getFIRMasterDetail($data['fir_id']);
            if($data['sample_qty'] > $firData->qty){
                $errorMessage['sample_qty'] = "Inspection Qty is invalid.";
            }
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->fir->saveSampleQty($data);
            $this->printJson($result);
        endif;
    }

    public function firRework(){
        $this->data['headData']->pageTitle = "FIR Rework";
        $this->data['prod_type'] = 2;
        $this->data['tableHeader'] = getProductionHeader('approvedLog');
        $this->load->view("fir/fir_rework_index",$this->data);
    }

    public function getApprovedProductionLogDTRows($prod_type=2,$is_approve=1){
        $data = $this->input->post(); $data['prod_type'] = $prod_type; $data['is_approve'] = $is_approve;
        $result = $this->productionLog->getProductionLogDTRows($data); 
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->edit_disabled = 1;
            $sendData[] = getApprovedProductionLogData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function testReport(){
        $data = $this->input->post();
        $this->data['testReportData'] = $this->fir->getTCData(['job_card_id' => $data['id']]);
        $this->load->view($this->testReport,$this->data);
    }
}
