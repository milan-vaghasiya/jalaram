<?php 
class NpdMaterialIssue extends MY_Controller{
    private $indexPage = "npd_issue/index";
    private $form = "npd_issue/form";
    private $requestForm = "job_material_dispatch/purchase_request";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Npd Material Issue";
		$this->data['headData']->controller = "npdMaterialIssue";
		$this->data['headData']->pageUrl = "npdMaterialIssue";
	}
	
    public function index($status=0){
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getStoreDtHeader('npdMaterialIssue');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->npdMaterialIssue->getNpdRequestData($data); 
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
           
            $sendData[] = getNpdIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function issueMaterial()
    {
        $this->data['jobCardData'] = $this->jobcard_v3->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if(empty($data['batch_no'][0]))
            $errorMessage['general_batch_no'] = "Location and Batch No. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $data['dispatch_by'] = $this->loginId;
            $data['created_by'] = $this->loginId;
            $this->printJson($this->npdMaterialIssue->saveNpdMaterial($data));
        endif;
    }

    //CHanged By Karmi @09/08/2022
    public function edit(){
        $id = $this->input->post('id');
        $dispatchData = $this->npdMaterialIssue->getNpdMaterialIssue($id);
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->data['dataRow'] = $dispatchData; 
        if(!empty($dispatchData->location_id))     
        {
            $this->data['batchData']=$this->item->locationWiseBatchStock($dispatchData->req_item_id,$dispatchData->location_id);
        } 
        
        $this->data['batchTrans'] = $this->npdMaterialIssue->getNpdIssueBatchTrans($id);
        $this->load->view($this->form,$this->data);
    }

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->npdMaterialIssue->delete($id));
        endif;
    }

    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists('2,3');
        $this->data['nextReqNo'] = $this->jobMaretialRequest->nextReqNo();
        $this->load->view($this->requestForm,$this->data);
    }

    //Change By Avruti @16/08/2022
    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = [
				'id' => $data['id'],
				'req_item_id' => $data['req_item_id'],
				'req_qty' => $data['req_qty'],
				'req_item_name' => $data['req_item_name']
			];
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data,$itemData));
        endif;
    }
    
}
?>