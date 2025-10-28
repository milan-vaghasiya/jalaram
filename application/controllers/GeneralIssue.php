<?php 
class GeneralIssue extends MY_Controller{
    private $indexPage = "general_issue/index";
    private $form = "general_issue/form";
    private $viewPage = "general_issue/trans_view";
    private $pendingRequestPage = "general_issue/pending_request";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "General Issue";
		$this->data['headData']->controller = "generalIssue";
		$this->data['headData']->pageUrl = "generalIssue";
	}
	
    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader('pendingRequest');
        $this->load->view($this->pendingRequestPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->generalIssue->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $issueData=$this->generalIssue->getJobMaterial($row->id);
            $row->total_item=(!empty($issueData->trans_data))?count($issueData->trans_data):0;
           
            $sendData[] = getGeneralIssueData($row);
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

    public function getItemLocationList()
    {
        $data=$this->input->post();
        $locationData = $this->item->itemWiseStock($data['item_id']);
        $options = '<option value="">Select Location</option>';
        foreach ($locationData as $row) :
            

                $options.='<option value="' . $row->id . '" data-store_name="' . $row->store_name . '" >' . $row->location . ' </option>';
            endforeach;
           
        $this->printJson(['status'=>1,'options'=>$options]);
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

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_id'][0]))
            $errorMessage['general_batch_no'] = "Item Name is required.";
       
        if(empty($data['batch_no'][0]))
            $errorMessage['general_batch_no'] = "Location and Batch No. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $data['dispatch_by'] = $this->loginId;
            $data['created_by'] = $this->loginId;
            $this->printJson($this->generalIssue->save($data));
        endif;
    }

    //CHanged By Karmi @09/08/2022
    public function edit(){
        $id = $this->input->post('id');
        $dispatchData = $this->generalIssue->getJobMaterial($id);
        if(!empty($dispatchData->req_no))
        {   $this->data['itemData'] = $this->generalIssue->getRequestedItemForReqNo($dispatchData->req_no);
            $this->data['requested_id'] = $dispatchData->created_by;
        }
        else{
            $this->data['itemData'] = $this->item->getItemList(2);
        }
        $this->data['locationData'] = $this->store->getStoreLocationList();
        
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['dataRow'] = $dispatchData; 
        if(!empty($dispatchData->location_id))     
        {
            $this->data['batchData']=$this->item->locationWiseBatchStock($dispatchData->req_item_id,$dispatchData->location_id);
        }  
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->generalIssue->delete($id));
        endif;
    }

    public function viewMaterialIssueTrans()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->generalIssue->getJobMaterial($id);
        $this->load->view($this->viewPage,$this->data);
    }
    
    //Created By Karmi @03/08/2022
    public function materialIssue()
    {
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    public function getGeneralPendingRequestData()
    {
        $data = $this->input->post();
        $result = $this->jobMaterial->getGeneralPendingRequestDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->req_item_stock = (!empty($row->req_item_id))?$this->store->getItemStockRTD($row->req_item_id,3)->qty:"";
            $sendData[] = getGeneralPendingRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
}
?>