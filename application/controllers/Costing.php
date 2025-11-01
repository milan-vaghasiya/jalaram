<?php
class Costing extends MY_Controller
{
    private $indexPage = "costing/index";
    private $formPage = "costing/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Costing";
		$this->data['headData']->controller = "costing";
		$this->data['headData']->pageUrl = "costing";
	} 

    public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $data = $this->input->post();
        $result = $this->costingModel->getCostingDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$costingStatus = $this->costingModel->checkCostingStatus($row->id);

            $row->process = (!empty($costingStatus->process)) ?  $costingStatus->cstng.'/'.$costingStatus->process : '';
			
            $sendData[] = getCostingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['item_id'] = $id;   
        $this->data['dataRow'] = $this->costingModel->getProductCost($id);
        $this->data['bomData'] = $this->item->getProductKitData($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 
        $this->load->view($this->formPage,$this->data);
    }

    public function saveCosting(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['machining_process'] = "";$mproArray = array();
			if(isset($data['mprocess_id']) && !empty($data['mprocess_id'])):
				foreach($data['mprocess_id'] as $key=>$value):
					$mproArray[] = [
						'mprocess_id' => $value,
						'mhr' => $data['mhr'][$key],
						'c_time' => $data['c_time'][$key],
                        'mprocess_cost' => $data['mprocess_cost'][$key]
					];
				endforeach;
				$data['machining_process'] = json_encode($mproArray);
			endif;

            $data['secondary_process'] = "";$hproArray = array();
			if(isset($data['hprocess_id']) && !empty($data['hprocess_id'])):
				foreach($data['hprocess_id'] as $key=>$value):
					$hproArray[] = [
						'hprocess_id' => $value,
						'hprocess_cost' => $data['hprocess_cost'][$key]
					];
				endforeach;
				$data['secondary_process'] = json_encode($hproArray);
			endif; 
            unset($data['hprocess_cost'],$data['hprocess_id'],$data['mprocess_cost'],$data['mprocess_id'],$data['mhr'],$data['c_time']);

            $data['id'] = NULL;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->costingModel->saveCosting($data));
        endif;
    }

    public function productCosting(){
		$this->data['headData']->pageTitle = "Product Cost";
		$this->data['headData']->pageUrl = "costing/productCosting";
        $this->data['tableHeader'] = getProductionHeader('productCosting');
        $this->load->view('costing/product_cost',$this->data);
    }

    public function getPrdCostDTRows($status = 0){        
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->costingModel->getPrdCostDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProductCostingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function rmCostRequest(){
        $data= $this->input->post();
        $this->data['dataRow'] = $this->costingModel->getCostingData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['gradeList'] = $this->materialGrade->getMaterialGrades();
        $this->load->view('costing/rm_request_form',$this->data);
    }

    public function saveRmCostRequest(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['grade_id'])){ $errorMessage['grade_id'] = "Grade is required."; }
        // if(empty($data['dimension'])){ $errorMessage['dimension'] = "Dimension is required."; }
        if(empty($data['shape'])){ $errorMessage['shape'] = "Shape is required."; }
        if(empty($data['moq'])){ $errorMessage['moq'] = "MOQ is required."; }
        if(empty($data['gross_wt'])){ $errorMessage['gross_wt'] = "Weight is required."; }
        if(empty($data['total_gross_wt'])){ $errorMessage['total_gross_wt'] = "Total weight is required."; }

        if(empty($data['field1'])){ $errorMessage['field1'] = "This field is required."; }        
        if(empty($data['field2']) && (in_array($_POST['shape'],['rectangle','pipe','sheet']))){ 
            $errorMessage['field2'] = "This field is required."; 
        }        
        if(empty($data['field3'])){ $errorMessage['field3'] = "This field is required."; }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['rm_cost_request'] = 1;
            $data['rm_rate'] = 0;
            $this->printJson($this->costingModel->saveRequest($data));
        endif;
    }

    public function saveMfgCostRequest(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id'])){ $errorMessage['id'] = "Grade is required."; }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $postData = [
                'id'=>$data['id'],
                'mfg_cost_request'=>1,
            ];
            $this->printJson($this->costingModel->saveRequest($postData));
        endif;
    }

    public function addCostDetail(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->costingModel->getCostingData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['mfgProcessList'] = $this->costingModel->getProcessCostingData(['cost_id'=>$data['id']]);

        $shapes = $this->shapes();
        $this->data['shape'] = $shapes[$this->data['dataRow']->shape];
        
        // print_r($this->data['dataRow']); exit;
        $this->load->view('costing/costing_form',$this->data);
    }

    public function saveCostDetail(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])){
                $dataRow = $this->costingModel->getCostingData(['id'=>$data['ref_id'],'single_row'=>1]);
                $data['enq_id'] = $dataRow->enq_id;
                $data['dimension'] = $dataRow->dimension;
                $data['grade_id'] = $dataRow->grade_id;
                $data['cost_date'] = date("Y-m-d");
                
            }
            $this->printJson($this->costingModel->saveCostDetail($data));
        endif;
    }

    public function purchaseCostReq(){
		$this->data['headData']->pageTitle = "Purchase Costing";
        $this->data['tableHeader'] = getProductionHeader('purchaseCostReq');
        $this->load->view('costing/rm_cost_req_index',$this->data);
    }

    public function getPurCostReqDTRows($status = 0){        
        $data = $this->input->post();
        $data['status'] = $status;
        $data['req_type'] = 'RM';
        $result = $this->costingModel->getCostReqDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			
            $sendData[] = getPurchaseCostReq($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRmCost(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->costingModel->getCostingData(['id'=>$data['id'],'single_row'=>1]);

        $shapes = $this->shapes();
        $this->data['shape'] = $shapes[$this->data['dataRow']->shape];

        $this->load->view('costing/rm_cost',$this->data);
    }

    public function saveRmCost(){
        $data = $this->input->post();
        $errorMessage = array();
        // if(empty($data['dimension'])){ $errorMessage['dimension'] = "Dimension is required."; }
        if(empty($data['rm_rate'])){ $errorMessage['rm_rate'] = "Rate is required."; }
        if(empty($data['gross_wt'])){ $errorMessage['gross_wt'] = "Gross weight is required."; }

        if(empty($data['field1'])){ $errorMessage['field1'] = "This field is required."; }        
        if(empty($data['field2']) && (in_array($_POST['shape'],['rectangle','pipe','sheet']))){ 
            $errorMessage['field2'] = "This field is required."; 
        }        
        if(empty($data['field3'])){ $errorMessage['field3'] = "This field is required."; }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->costingModel->saveRequest($data));
        endif;
    }

    public function mfgCostReq(){
		$this->data['headData']->pageTitle = "Mfg. Costing";
        $this->data['tableHeader'] = getProductionHeader('mfgCostReq');
        $this->load->view('costing/mfg_cost_req_index',$this->data);
    }

    public function getMfgCostReqDTRows($status = 0){        
        $data = $this->input->post();
        $data['status'] = $status;
        $data['req_type'] = 'MFG';
        $result = $this->costingModel->getCostReqDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			
            $sendData[] = getMfgCostReq($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMfgCost(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow= $this->costingModel->getCostingData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view('costing/mfg_cost',$this->data);
    }

    public function saveMfgCost(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_id'])){ 
            $errorMessage['process_id'] = "Process is required."; 
        }
        else{
            $processData = $this->process->getProcess($data['process_id']);
            if($processData->is_machining == 'Yes'){
                if(empty($data['cycle_time'])){
                    $errorMessage['cycle_time'] = "Cycle Time is required."; 
                }else{
                    $costData = $this->costingModel->getCostingData(['id'=>$data['cost_id'],'single_row'=>1]);
                    $mhrData = $this->process->getProcessMhrDetail(['process_id'=>$data['process_id'],'grade_id'=>$costData->grade_id,'single_row'=>1]);
                    if(empty($mhrData->mhr)){
                        $errorMessage['process_id'] = "MHR is not set."; 
                    }else{
                        $data['mhr'] = $mhrData->mhr;
                        $data['process_cost'] = round((($data['cycle_time']/3600) * $data['mhr']),2);
                    }
                }
            }
            elseif($processData->is_machining == 'No' && empty($data['process_cost'])){
                $errorMessage['process_cost'] = "Process Cost is required."; 
            }
        }
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->costingModel->saveMfgCost($data));
        endif;
    }

    public function deleteMfgCost(){
        $data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->costingModel->deleteMfgCost($data));
		endif;

    }

    public function getProcessHtml(){
        $data = $this->input->post();
        $tbodyData = '';$i=1;
        $transData = $this->costingModel->getProcessCostingData(['cost_id'=>$data['cost_id']]);
        if(!empty($transData)){
            foreach($transData AS $row){
                $deleteParam = "{'id' : ".$row->id.",'cost_id' : ".$row->cost_id."}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashMfgCost('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                $tbodyData.='<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td>'.$row->process_name.'</td>
                                <td class="text-center">'.$row->cycle_time.'</td>
                                <td class="text-center">'.(($row->is_machining == 'No')?$row->process_cost:'').'</td>
                                <td class="text-center">'.$deleteBtn.'</td>
                             </tr>';
            }
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function printCostDetail($id){
		$this->data['companyData'] = $this->jobcard_v3->getCompanyInfo();
        $this->data['dataRow'] = $this->costingModel->getCostingData(['id'=>$id,'single_row'=>1]);
        $this->data['mfgProcessList'] = $this->costingModel->getProcessCostingData(['cost_id'=>$id]);

        $pdfData = $this->load->view('costing/costing_print',$this->data,true);

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 50]]);
		$pdfFileName='cost_'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));		
		$mpdf->AddPage('P','','','','',5,5,3,3,0,0,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

    public function approveCost(){
        $data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->costingModel->approveCost($data));
		endif;
    }

    public function reviseCostDetail(){
        $data = $this->input->post();
        $dataRow = $this->costingModel->getCostingData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['mfgProcessList'] = $this->costingModel->getProcessCostingData(['cost_id'=>$data['id']]);
        $dataRow->rev_no = $dataRow->rev_no + 1;
        $dataRow->ref_id = $dataRow->id;
        unset($dataRow->id);
        $this->data['dataRow'] = $dataRow;
        $this->load->view('costing/costing_form',$this->data);
    }

    public function getCostingRevList(){
        $data= $this->input->post();
        $this->data['costList'] = $this->costingModel->getCostingData(['enq_id'=>$data['id']]);
        $this->load->view('costing/revision_list',$this->data);
    }
    
    
    
    // Weight Calculator
    public function wcCalc(){
        $data= $this->input->post();
        $this->data['id'] = $data['id'];
        $this->load->view('costing/wc_form',$this->data);
    }

    public function shapes(){
        $shapes = [
            'round_dia' => 'Round Bar',
            'square' => 'Square Bar',
            'rectangle' => 'Rectangle Bar',
            'pipe' => 'Pipe / Tube',
            'hex' => 'Hexagonal Bar',
            'sheet' => 'Sheet / Plate'
        ];
        return $shapes;
    }
}
?>