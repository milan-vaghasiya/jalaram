<?php
class ProductionLog extends my_controller{
    private $indexPage = "production_v3/production_log/index";
    private $rejIndexPage = "production_v3/production_log/rej_index";
    private $ncIndexPage = "production_v3/production_log/nc_index";
    private $okformPage = "production_v3/production_log/ok_form";
    private $rejFormPage = "production_v3/production_log/rej_form";
    private $ncFormPage = "production_v3/production_log/nc_form";
    private $prd_log_index = "production_v3/production_log/prd_log_index";
    private $prd_rej_index = "production_v3/production_log/prd_rej_index";
    private $prd_nc_index = "production_v3/production_log/prd_nc_index";
    private $holdLogForm = "production_v3/production_log/hold_log_form";
    private $reworkManage="production_v3/production_log/rework_manage";

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Production Log";
        $this->data['headData']->controller = "production_v3/productionLog";
       
    }

    public function index(){
        $this->data['headData']->pageTitle = "Production Log";   
        $this->data['tableHeader'] = getProductionHeader('pendingProductionLog');
        $this->load->view($this->indexPage,$this->data);
    }

    public function productionLog(){
        $this->data['headData']->pageTitle = "Production Log";  
        $this->data['tableHeader'] = getProductionHeader('logSheet');
        $this->load->view($this->prd_log_index,$this->data);
    }

    public function rejectionIndex($prod_type =1){
        $this->data['headData']->pageTitle = "Quality Log";
        $this->data['prod_type'] = $prod_type;
        $this->data['tableHeader'] = getProductionHeader('pendingApprovedLog');
        $this->load->view($this->rejIndexPage,$this->data);
    }

    public function rejectionLog($prod_type =1){
        $this->data['headData']->pageTitle = "Quality Log";
        $this->data['prod_type'] = $prod_type;
        $this->data['tableHeader'] = getProductionHeader('approvedLog');
        $this->load->view($this->prd_rej_index,$this->data);
    }

    public function addProductionLogApproval(){
        $data= $this->input->post();
        // $this->data['jobCardData'] = $this->jobcard_v3->getJobcardListByVersion(2, '0,1,2,4');
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();


        $dataRow = $this->productionLog->getLogs($this->input->post('id'));
        $this->data['rejRwData'] = $this->productionLog->getRejRwData($this->input->post('id'));

        //Process
        $jobData = $this->jobcard_v3->getJobcard($dataRow->job_card_id);
        $processList = explode(',', $jobData->process);
        $dataRow->processOpt = '';
      

        //Machine
        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach ($machineData as $row) :
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
            $dataRow->machineOpt .= '<option value="' . $row->id . '" ' . $selectMac . '>[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
        endforeach;

        //Rej & Rew Form
        $in_process_key = array_keys($processList, $dataRow->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($processList as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $dataRow->stage = $html;
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->rejFormPage, $this->data);
    }

    public function getProductionLogDTRows($prod_type=1,$is_approve=0){
        $data = $this->input->post(); $data['prod_type'] = $prod_type; $data['is_approve'] = $is_approve;
        $result = $this->productionLog->getProductionLogDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProductionLogData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getApprovedProductionLogDTRows($prod_type=1,$is_approve=1){
        $data = $this->input->post(); $data['prod_type'] = $prod_type;$data['is_approve'] = $is_approve;
        $result = $this->productionLog->getProductionLogDTRows($data); 
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getApprovedProductionLogData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function ncIndex(){
        $this->data['headData']->pageTitle = "Final Inspection";
        $this->data['tableHeader'] = getProductionHeader('pendingProductionLog');
        $this->load->view($this->ncIndexPage,$this->data);
    }

    public function ncLog(){
        $this->data['headData']->pageTitle = "Final Inspection";
        $this->data['tableHeader'] = getProductionHeader('logSheet');
        $this->load->view($this->prd_nc_index,$this->data);
    }

    public function getPendingLogDtRows($prod_type=1,$log_type=0){
        $data = $this->input->post(); $data['prod_type'] = $prod_type;$data['log_type'] = $log_type;
        $result = $this->productionLog->getPendingLogDtRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->prod_type = $prod_type;
            $row->log_type = $log_type;
            $sendData[] = getPendingProductionLog($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProductionLog(){
        $data = $this->input->post();
        $dataRow = $this->processMovement->getApprovalData(['id'=>$data['id']]); 
        $dataRow->job_approval_id = $dataRow->id;
        unset($dataRow->id);
        $dataRow->process_id = $dataRow->in_process_id;
        $ctData=$this->process->getProductProcess(['process_id'=>$dataRow->process_id,'item_id'=>$dataRow->product_id]);
		$dataRow->m_ct = (!empty($ctData)) ? $ctData->master_ct : 0 ;
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
       

        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach($machineData as $row):
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id)?"selected":"";
            $dataRow->machineOpt .= '<option value="'.$row->id.'" '.$selectMac.'>[ '.$row->item_code.' ] '.$row->item_name.'</option>';
        endforeach;

        //Rej & Rew Form
        $processList = explode(',', $dataRow->process);
        $in_process_key = array_keys($processList, $dataRow->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($processList as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $dataRow->stage = $html;
        $this->data['dataRow'] = $dataRow;
        if($data['log_type'] == 0){
            $this->load->view($this->okformPage, $this->data);
        }
        if($data['log_type'] == 1){
            $this->load->view($this->rejFormPage, $this->data);
        }
        if($data['log_type'] == 2){
            $this->data['operatorList'] = $this->employee->getFinalInspectorList();
            $this->data['inspectionTypeList']=$this->inspectionType->getInspectionTypeList();
            $this->load->view($this->ncFormPage, $this->data);
        }   
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['log_date']) && empty($data['is_approve'])){
            $errorMessage['log_date'] = "Date is required.";
        }else{
            if(!empty($data['log_date'])){
                if($data['log_date'] > date('Y-m-d')){
                    $errorMessage['log_date'] = "Future Date not allowed.";
                }
            }
        }
        if(empty($data['load_unload_time']) && empty($data['is_approve'])){
            $data['load_unload_time'] = 0;
        }
        if(empty($data['production_time']) && $data['log_type'] == 0){
            $errorMessage['production_time'] = "Production Time is required."; 
        }
        if(empty($data['cycle_time']) && $data['log_type'] == 0){
            $errorMessage['cycle_time'] = "Cycle Time is required."; 
        }
        if(empty($data['job_card_id'])){
            $errorMessage['job_card_id'] = "Job Card is required.";
        }
        if(empty($data['process_id'])){
            $errorMessage['process_id'] = "Process is required.";
        }
        $machineData = $this->process->getProcess($data['process_id']); 
        
        if(empty($data['machine_id'])  && $data['prod_type'] ==1 && $machineData->is_machining == 'Yes'){
            $errorMessage['machine_id'] = "Machine is required.";
        }
        if(empty($data['operator_id'])  && $data['prod_type'] ==1){
            $errorMessage['operator_id'] = "Inspector is required.";
        }
        // IF Entry FRom Ok ANF NC Report
        if(empty($data['production_qty']) ){
            $errorMessage['production_qty'] = "Qty is required.";
        }
        
        if(!empty($data['machine_id']) && !empty($data['log_date'])){
            $logTimeData = $this->productionLog->getMachineRuntime(['log_date'=>$data['log_date'],'machine_id'=>$data['machine_id']]);
            $total_runtime = (!empty($logTimeData->total_runtime))?$logTimeData->total_runtime:0;
            $min_sum = $total_runtime + $data['production_time'];
            if ($min_sum > 1440) {
                $errorMessage['production_time'] = "Time is greater than 24 Hours."; 
            }
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $oldQty=0;
            if(!empty($data['id'])){
                $oldQty = $this->productionLog->getLogs($data['id'])->production_qty;
            }
         
			$JobApproveData=$this->processMovement->getApprovalData(['job_card_id'=>$data['job_card_id'],'in_process_id'=>$data['process_id']]);
            $logData=$this->productionLog->getPrdLogOnProcessNJob(['job_approval_id'=>$data['job_approval_id'],'prod_type'=>(($data['prod_type'] == 3)?$data['prod_type']:[1,5])]);
            $vdrData = $this->productionLog->getJobworkQty($data['job_card_id'],$data['process_id']);
            $vendor_qty = $vdrData->vendor_qty -  $vdrData->without_process_qty;
            if($data['prod_type'] == 3){
                $pending_qty = $vendor_qty;
            }else{
                $pending_qty = (($JobApproveData->in_qty - ($logData->ok_qty + $logData->rejection_qty + $logData->rework_qty)) - $vendor_qty) + $oldQty;
            } 
			
            $jobData = $this->jobcard_v3->getJobcard($data['job_card_id']);
            $jobProcess = explode(",", $jobData->process);
           
            if(!isset($data['is_approve'])){
                $data['ok_qty'] = $data['production_qty'];
            }
            $data['rw_reason']='';$data['rej_reason']='';
           
            if(!empty($data['idle_reason'])):
                $data['idle_time'] = array_sum(array_column($data['idle_reason'],'idle_time'));
                $data['idle_reason'] = json_encode($data['idle_reason']);
            endif;

            $data['rw_qty'] = 0;
            if(!empty($data['rework_reason'])):
                $data['rw_qty'] = array_sum(array_column($data['rework_reason'],'rw_qty'));
                $data['rw_reason'] = json_encode($data['rework_reason']);
            endif;

            $data['rej_qty'] = 0;
            if(!empty($data['rejection_reason'])):
                $data['rej_qty'] = array_sum(array_column($data['rejection_reason'],'rej_qty'));
                $data['rej_reason'] = json_encode($data['rejection_reason']);
            endif;
            
            //print_r('(('.$logData->ok_qty.' + '.$logData->rejection_qty.' + '.$logData->rework_qty.' + '.$oldQty.') - '.$JobApproveData->in_qty.') - '.$vendor_qty);
            //print_r(' ** '.$pending_qty);
            
            if ($data['production_qty'] > $pending_qty) {
                $errorMessage['general_error'] = "Qty is greater than pending qty.";
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            }
             
            unset($data['log_type']);
            if(!empty($data['is_approve'])){
                $data['is_approve']= $this->loginId;
                $data['approved_at']= date("Y-m-d");
            }else{
                $data['created_by'] = $this->session->userdata('loginId'); 
            }
            
            $this->printJson($this->productionLog->save($data));
        endif;
    }

    public function getProductionDTRows($prod_type=0,$log_type = 0){
        $data = $this->input->post(); $data['prod_type'] = $prod_type;$data['log_type'] = $log_type;
        $result = $this->productionLog->getProductionDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->log_type = $log_type;
            $sendData[] = getLogSheetData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getRejFrom()
    {
        $data = $this->input->post();
        $vendorData = $this->productionLog->getJobWorkVendorTransData($data);
        $rejOption = '<option value="0" data-party_name="In House">In House</option>';
        $rewOption = '<option value="0" data-party_name="In House">In House</option>';
        if (!empty($vendorData)) :
            foreach ($vendorData as $row) :
                $rejOption .= '<option value="' . $row->vendor_id . '" data-party_name="' . $row->party_name . '">' . $row->party_name . '</option>';
                $rewOption .= '<option value="' . $row->vendor_id . '" data-party_name="' . $row->party_name . '">' . $row->party_name . '</option>';
            endforeach;
        endif;
        $this->printJson(['status' => 1, 'rejOption' => $rejOption, 'rewOption' => $rewOption]);
    }

    public function edit()
    {
        $data= $this->input->post();
        $this->data['jobCardData'] = $this->jobcard_v3->getJobcardListByVersion(2, '0,1,2,4');
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();


        $dataRow = $this->productionLog->getLogs($this->input->post('id'));
        $this->data['rejRwData'] = $this->productionLog->getRejRwData($this->input->post('id'));

        //Process
        $jobData = $this->jobcard_v3->getJobcard($dataRow->job_card_id);
        $processList = explode(',', $jobData->process);
        
        //Machine
        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach ($machineData as $row) :
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
            $dataRow->machineOpt .= '<option value="' . $row->id . '" ' . $selectMac . '>[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
        endforeach;

        //Rej & Rew Form
        $in_process_key = array_keys($processList, $dataRow->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($processList as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $dataRow->stage = $html;

        $this->data['dataRow'] = $dataRow;

        if($data['log_type'] == 0){
            $this->load->view($this->okformPage, $this->data);
        }
        if($data['log_type'] == 1){
            $this->load->view($this->rejFormPage, $this->data);
        }
        if($data['log_type'] == 2){
            $this->data['operatorList'] = $this->employee->getFinalInspectorList();
            $this->data['inspectionTypeList']=$this->inspectionType->getInspectionTypeList();
            $this->load->view($this->ncFormPage, $this->data);
        }
    }

    public function delete(){
        $id = $this->input->post('id');
        $data = $this->rejectionLog->getLogs($this->input->post('id'));

        if($data->ok_qty > 0 &&  $data->is_approve > 0){
			$JobApproveData=$this->processMovement->getApprovalData(['job_card_id'=>$data->job_card_id,'in_process_id'=>$data->process_id]);
            $pending_qty = $JobApproveData->total_ok_qty - $JobApproveData->out_qty;
           
            if($data->ok_qty > $pending_qty){
                $this->printJson(['status'=>0,'message'=>'You can not delete this log. because ok qty is moved to next process']);
            }
        }
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->productionLog->delete($id));
        endif;
    }
    
    public function viewLogData(){
        $data = $this->input->post();
        $dataRow = $this->productionLog->getLogs($data['id']); 
        
        $ctData=$this->process->getProductProcess(['process_id'=>$dataRow->process_id,'item_id'=>$dataRow->product_id]);
		$dataRow->m_ct = (!empty($ctData)) ? $ctData->master_ct : 0 ;
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
       

        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach($machineData as $row):
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id)?"selected":"";
            $dataRow->machineOpt .= '<option value="'.$row->id.'" '.$selectMac.'>[ '.$row->item_code.' ] '.$row->item_name.'</option>';
        endforeach;
        
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->okformPage, $this->data);
    }

    public function reworkmanagement($id)
    {
        $this->data['rejRwData']=$this->rejectionLog->getRejectionData($id,2);
        $this->data['dataRow'] = $this->rejectionLog->getLogs($id);
        $this->data['rejectionComments'] = $this->comment->getCommentList();
      
        $jobCardData = $this->jobcard_v3->getJobcard($this->data['dataRow']->job_card_id);
        $jobProcess = explode(",", $jobCardData->process);
        $in_process_key = array_keys($jobProcess, $this->data['dataRow']->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($jobProcess as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $this->data['dataRow']->stage=$html;
        $this->data['pageTitle'] = getPrefixNumber($jobCardData->job_prefix,$jobCardData->job_no).' ['.$jobCardData->product_code.']';
        $this->load->view($this->reworkManage, $this->data);
    }

    public function saveRejectionQty(){
        $data=$this->input->post();
        $logData=$this->productionLog->getRejectionData($data['id']);
        $data['rejection_reason']=[];
        $qty=0;
        if(!empty($logData)){
            foreach($logData as $row){
                $data['rejection_reason'][]=[
                    'trans_id'=>$row->id,
                    'rej_reason'=>$row->reason,
                    'rej_from'=>$row->vendor_id,
                    'rej_remark'=>$row->remark,
                    'rej_party_name'=>$row->vendor_name,
                    'rej_stage'=>$row->belongs_to,
                    'rej_stage_name'=>$row->belongs_to_name,
                    'rej_ref_id'=>$row->ref_id,
                    'rej_type'=>$row->rej_type,
                    'rej_qty'=>$row->qty,
                    'rejection_reason'=>$row->reason_name,
                ];
                $qty+=$row->qty;
            }
        }
        $data['rejection_reason'][]=[
            'trans_id'=>'',
            'rej_qty'=>$data['rej_qty'],
            'rej_reason'=>$data['rej_reason'],
            'rejection_reason'=>$data['rejection_reason_name'],
            'rej_from'=>$data['rej_from'],
            'rej_remark'=>$data['rej_remark'],
            'rej_party_name'=>$data['rej_party_name'],
            'rej_stage'=>$data['rej_stage'],
            'rej_stage_name'=>$data['rej_stage_name'],
            'rej_ref_id'=>$data['rej_ref_id'],
            'rej_type'=>$data['rej_type']
        ];

        // For minus qty from rework log and production log
        $reworkData=[
            'id'=>$data['rej_ref_id'],
            'rej_qty'=>$data['rej_qty']
        ];
        $data['rej_qty']=$data['rej_qty']+$qty;
        $data['created_by'] = $this->session->userdata('loginId');
        unset($data['trans_id'],$data['rej_reason'],$data['rejection_reason_name'],$data['rej_from'],$data['rej_remark'],$data['rej_party_name'],$data['rej_stage'],$data['rej_stage_name'],$data['rej_ref_id'],$data['rej_type']);
        $this->printJson($this->productionLog->save($data,$reworkData));
    }

    public function saveReworkQty(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->productionLog->saveReworkQty($data));
    }

    public function holdQtyLog(){
        $data= $this->input->post();
        $dataRow = $this->productionLog->getLogs($this->input->post('id'));
       
        //Process
        $jobData = $this->jobcard_v3->getJobcard($dataRow->job_card_id);
        $processList = explode(',', $jobData->process);
        $dataRow->processOpt = '';
      
        //Rej & Rew Form
        $in_process_key = array_keys($processList, $dataRow->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($processList as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $dataRow->stage = $html;
        $this->data['dataRow'] = $dataRow;
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->load->view($this->holdLogForm, $this->data);
    }

    public function saveHoldQtyLog(){
        $data = $this->input->post();
        $errorMessage = array();
        $logData=$this->productionLog->getLogs($data['id']);

        $data['rw_reason']='';$data['rej_reason']='';
        
        if(!empty($data['idle_reason'])):
            $data['idle_time'] = array_sum(array_column($data['idle_reason'],'idle_time'));
            $data['idle_reason'] = json_encode($data['idle_reason']);
        endif;

        $data['rw_qty'] = 0;
        if(!empty($data['rework_reason'])):
            $data['rw_qty'] = array_sum(array_column($data['rework_reason'],'rw_qty'));
            $data['rw_reason'] = json_encode($data['rework_reason']);
        endif;

        $data['rej_qty'] = 0;
        if(!empty($data['rejection_reason'])):
            $data['rej_qty'] = array_sum(array_column($data['rejection_reason'],'rej_qty'));
            $data['rej_reason'] = json_encode($data['rejection_reason']);
        endif;

        $totalQty = $data['ok_qty']+$data['rej_qty']+$data['rw_qty'];
        if ($totalQty > $logData->hold_qty) {
            $errorMessage['general_error'] = "Qty is greater than Hold Qty";
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        }elseif($data['ok_qty'] < 0){
            $errorMessage['ok_qty'] = "Qty is invalid";
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        }
            
        if(!empty($data['is_approve'])){
            $data['is_approve']= $this->loginId;
            $data['approved_at']= date("Y-m-d");
        }else{
            $data['created_by'] = $this->session->userdata('loginId'); 
        }
        $this->printJson($this->productionLog->saveHoldQtyLog($data));
    }
}
?>